<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdPayment extends Model
{
    protected $fillable = [
        'advertisement_id',
        'payment_ref',
        'btc_address',
        'amount_usd',
        'amount_btc',
        'btc_rate_snapshot',
        'status',
        'tx_hash',
        'confirmations',
        'detected_at',
        'confirmed_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'amount_usd'        => 'decimal:2',
            'amount_btc'        => 'decimal:8',
            'btc_rate_snapshot' => 'decimal:2',
            'confirmations'     => 'integer',
            'detected_at'       => 'datetime',
            'confirmed_at'      => 'datetime',
            'expires_at'        => 'datetime',
        ];
    }

    public function advertisement(): BelongsTo
    {
        return $this->belongsTo(Advertisement::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast() && !in_array($this->status, ['detected', 'confirming', 'confirmed']);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'confirmed';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'awaiting'   => 'Awaiting Payment',
            'detected'   => 'Payment Detected',
            'confirming' => 'Confirming on Blockchain',
            'confirmed'  => 'Payment Confirmed',
            'expired'    => 'Payment Expired',
            'overpaid'   => 'Overpaid — Contact Us',
            default      => ucfirst($this->status),
        };
    }

    /** Generate the BIP21-formatted payment URI for QR codes */
    public function bip21Uri(): string
    {
        $btc = rtrim(rtrim(number_format((float) $this->amount_btc, 8, '.', ''), '0'), '.');
        return sprintf(
            'bitcoin:%s?amount=%s&label=HiddenLine+Ad&message=%s',
            $this->btc_address,
            $btc,
            urlencode($this->payment_ref)
        );
    }
}
