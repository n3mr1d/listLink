<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->text('description')->default('No description provided.');
            $table->string('url')->unique();
            $table->string('slug')->unique();
            $table->string('category')->default('other');
            $table->string('status')->default('pending'); // pending, active, rejected
            $table->string('uptime_status')->default('unknown'); // online, offline, timeout, unknown
            $table->dateTime('last_check')->nullable();
            $table->unsignedInteger('check_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
