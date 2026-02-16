<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('uptime_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_id')->constrained('links')->onDelete('cascade');
            $table->string('checked_by_ip_hash', 64); // SHA-256 hashed IP
            $table->string('status'); // online, offline, timeout
            $table->unsignedInteger('response_time_ms')->nullable();
            $table->timestamp('checked_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uptime_logs');
    }
};
