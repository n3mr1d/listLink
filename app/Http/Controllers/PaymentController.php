<?php

namespace App\Http\Controllers;

use App\Models\AdPayment;
use App\Models\Advertisement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PaymentController extends Controller
{
    /**
     * Show the payment page for a given advertisement.
     */
    public function show(int $id)
    {
        $ad = Advertisement::findOrFail($id);

        if ($ad->payment_status === 'paid' || $ad->status === 'active') {
            return redirect()->route('dashboard.ads')
                ->with('info', 'This advertisement has already been paid and is being processed.');
        }

        // If already have an active (non-expired) payment session, reuse it
        $payment = AdPayment::where('advertisement_id', $id)
            ->whereNotIn('status', ['expired', 'confirmed'])
            ->latest()
            ->first();

        if (!$payment || $payment->isExpired()) {
            // Create new payment session
            $btcRate = $this->fetchBtcRate();
            $usd     = (float) ($ad->price_usd ?? 30);
            $btc     = $btcRate ? round($usd / $btcRate, 8) : 0;

            $payment = AdPayment::create([
                'advertisement_id'  => $ad->id,
                'payment_ref'       => 'HL-' . strtoupper(substr(md5($ad->id . time()), 0, 8)),
                'btc_address'       => config('services.btc_address', ''),
                'amount_usd'        => $usd,
                'amount_btc'        => $btc,
                'btc_rate_snapshot' => $btcRate ?? 0,
                'status'            => 'awaiting',
                'expires_at'        => now()->addHours(24),
            ]);
        }

        // ── Generate QR code server-side as inline SVG ──────────────────
        $qrSvg = null;
        if (!empty($payment->btc_address)) {
            try {
                // Build URI — if BTC amount is 0 (rate fetch failed), just use address
                $uri = (float) $payment->amount_btc > 0
                    ? $payment->bip21Uri()
                    : 'bitcoin:' . $payment->btc_address;

                $raw = QrCode::format('svg')
                    ->size(200)
                    ->margin(1)
                    ->errorCorrection('M')
                    ->generate($uri);

                $svg = (string) $raw;
                if (!empty(trim($svg))) {
                    $qrSvg = 'data:image/svg+xml;base64,' . base64_encode($svg);
                }
            } catch (\Throwable $e) {
                Log::error('QR code generation failed', [
                    'payment_id' => $payment->id,
                    'btc_address' => $payment->btc_address,
                    'amount_btc'  => $payment->amount_btc,
                    'error'       => $e->getMessage(),
                ]);
            }
        } else {
            Log::warning('QR skipped: No BTC address — set BTC_PAYMENT_ADDRESS in .env');
        }

        return view('payment', compact('payment', 'ad', 'qrSvg'));
    }

    /**
     * User manually submits a transaction ID for their payment.
     */
    public function submitTxid(Request $request, int $id)
    {
        $request->validate([
            'txid' => 'required|string|min:10|max:200|regex:/^[a-fA-F0-9]+$/',
        ], [
            'txid.regex' => 'Transaction ID should only contain hexadecimal characters.',
        ]);

        $payment = AdPayment::findOrFail($id);

        // Only allow TXID submission on awaiting/detected payments
        if (in_array($payment->status, ['confirmed', 'expired'])) {
            return redirect()->route('payment.show', $id)
                ->with('error', 'This payment has already been ' . $payment->status . '.');
        }

        $payment->update([
            'tx_hash'    => $request->txid,
            'status'     => 'detected',
            'detected_at' => $payment->detected_at ?? now(),
        ]);

        Log::info('Payment TXID submitted manually', [
            'payment_id' => $payment->id,
            'tx_hash'    => $request->txid,
        ]);

        return redirect()->route('payment.show', $id)
            ->with('success', 'Transaction ID submitted! We will verify your payment within 24 hours.');
    }

    /**
     * AJAX: poll blockchain to check payment status.
     */
    public function checkStatus(int $id): JsonResponse
    {
        $payment = AdPayment::findOrFail($id);

        // Already confirmed/expired — return immediately
        if (in_array($payment->status, ['confirmed', 'expired'])) {
            return response()->json($this->statusPayload($payment));
        }

        // Mark expired if past window
        if ($payment->isExpired()) {
            $payment->update(['status' => 'expired']);
            return response()->json($this->statusPayload($payment));
        }

        // Query mempool.space for transactions to this address
        $detected = $this->scanBlockchain($payment);

        if ($detected) {
            $payment->refresh();
        }

        return response()->json($this->statusPayload($payment));
    }

    // ──────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ──────────────────────────────────────────────────────────────────────

    private function statusPayload(AdPayment $payment): array
    {
        return [
            'status'        => $payment->status,
            'label'         => $payment->statusLabel(),
            'confirmations' => $payment->confirmations,
            'tx_hash'       => $payment->tx_hash,
            'amount_btc'    => (string) $payment->amount_btc,
            'amount_usd'    => (string) $payment->amount_usd,
            'expires_at'    => $payment->expires_at->toIso8601String(),
            'is_expired'    => $payment->isExpired() || $payment->status === 'expired',
            'is_confirmed'  => $payment->status === 'confirmed',
        ];
    }

    /**
     * Query mempool.space API for incoming transactions.
     * Returns true if payment state changed.
     */
    private function scanBlockchain(AdPayment $payment): bool
    {
        $address = $payment->btc_address;
        if (!$address) {
            return false;
        }

        $cacheKey = "btc_txs_{$address}";
        $changed  = false;

        try {
            // Cache API response for 30 seconds to avoid hammering
            $txs = Cache::remember($cacheKey, 30, function () use ($address) {
                $response = Http::timeout(10)
                    ->withHeaders(['User-Agent' => 'HiddenLine-PaymentGateway/1.0'])
                    ->get("https://mempool.space/api/address/{$address}/txs");

                if ($response->successful()) {
                    return $response->json();
                }
                return null;
            });

            if (!$txs || !is_array($txs)) {
                return false;
            }

            $expectedSatoshis = (int) round($payment->amount_btc * 1e8);
            // Allow ±1% tolerance for rounding differences
            $minSatoshis = (int) ($expectedSatoshis * 0.99);
            $maxSatoshis = (int) ($expectedSatoshis * 1.50); // over-payment detection

            foreach ($txs as $tx) {
                // Check outputs (vouts) for our address
                $received = 0;
                foreach ($tx['vout'] ?? [] as $vout) {
                    $scriptAddr = $vout['scriptpubkey_address'] ?? null;
                    if ($scriptAddr === $address) {
                        $received += (int) ($vout['value'] ?? 0);
                    }
                }

                if ($received < $minSatoshis) {
                    continue; // not our payment
                }

                $txId    = $tx['txid'] ?? null;
                $confirms = isset($tx['status']['block_height'])
                    ? (int) (now()->timestamp - ($tx['status']['block_time'] ?? 0)) // rough
                    : 0;
                // Use confirmed field from mempool directly
                $isConfirmed = ($tx['status']['confirmed'] ?? false) === true;
                $blockConfs  = $isConfirmed ? max(1, (int) ($tx['status']['block_height'] ?? 1)) : 0;

                $overpaid = $received > $maxSatoshis;
                $newStatus = match(true) {
                    $overpaid     => 'overpaid',
                    $isConfirmed  => 'confirmed',
                    default       => 'detected',
                };

                $updates = [
                    'tx_hash'       => $txId,
                    'confirmations' => $blockConfs,
                    'status'        => $newStatus,
                ];

                if (!$payment->detected_at) {
                    $updates['detected_at'] = now();
                }
                if ($isConfirmed && !$payment->confirmed_at) {
                    $updates['confirmed_at']     = now();
                    // Also mark the advertisement as paid
                }

                $payment->update($updates);

                if ($isConfirmed) {
                    $payment->advertisement()->update([
                        'payment_status' => 'paid',
                        'status'         => 'pending', // pending admin review
                    ]);
                }

                $changed = true;
                break; // Stop at first matching tx
            }
        } catch (\Throwable $e) {
            Log::warning('PaymentController: mempool scan failed', ['error' => $e->getMessage()]);
        }

        return $changed;
    }

    /**
     * Fetch BTC/USD rate with caching.
     */
    private function fetchBtcRate(): ?float
    {
        return Cache::remember('btc_usd_rate', 60, function () {
            try {
                $res = Http::timeout(8)->get('https://api.coingecko.com/api/v3/simple/price', [
                    'ids' => 'bitcoin', 'vs_currencies' => 'usd',
                ]);
                if ($res->successful()) {
                    return (float) ($res->json()['bitcoin']['usd'] ?? 0) ?: null;
                }
            } catch (\Throwable) {}

            try {
                $res = Http::timeout(8)->get('https://blockchain.info/ticker');
                if ($res->successful()) {
                    return (float) ($res->json()['USD']['last'] ?? 0) ?: null;
                }
            } catch (\Throwable) {}

            return null;
        });
    }
}
