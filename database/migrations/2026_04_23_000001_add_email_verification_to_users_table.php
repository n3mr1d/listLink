<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->unique()->after('username');
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->string('email_verification_token', 64)->nullable()->after('email_verified_at');
            $table->string('email_verification_code', 8)->nullable()->after('email_verification_token');
            $table->timestamp('email_verification_sent_at')->nullable()->after('email_verification_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'email',
                'email_verified_at',
                'email_verification_token',
                'email_verification_code',
                'email_verification_sent_at',
            ]);
        });
    }
};
