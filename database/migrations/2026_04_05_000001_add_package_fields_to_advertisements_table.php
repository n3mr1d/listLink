<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->string('package_tier')->nullable()->after('placement'); // starter, basic, standard, premium, pro, elite
            $table->unsignedInteger('price_usd')->nullable()->after('package_tier');
            $table->string('btc_address')->nullable()->after('price_usd');
            $table->string('payment_status')->default('unpaid')->after('btc_address'); // unpaid, pending, paid
        });
    }

    public function down(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->dropColumn(['package_tier', 'price_usd', 'btc_address', 'payment_status']);
        });
    }
};
