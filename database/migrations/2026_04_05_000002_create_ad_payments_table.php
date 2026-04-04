<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ad_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertisement_id')->constrained()->cascadeOnDelete();
            $table->string('payment_ref', 32)->unique(); // e.g. "HL-A1B2C3D4"
            $table->string('btc_address', 64);           // wallet address
            $table->decimal('amount_usd', 10, 2);        // USD price at time of creation
            $table->decimal('amount_btc', 16, 8);        // BTC price at time of creation
            $table->decimal('btc_rate_snapshot', 12, 2); // BTC/USD rate used
            $table->string('status', 20)->default('awaiting');
            // awaiting | detected | confirming | confirmed | expired | overpaid
            $table->string('tx_hash', 128)->nullable();  // blockchain tx id
            $table->unsignedInteger('confirmations')->default(0);
            $table->timestamp('detected_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('expires_at');             // payment window (24h)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_payments');
    }
};
