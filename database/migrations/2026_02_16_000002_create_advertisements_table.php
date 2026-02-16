<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->text('description')->nullable();
            $table->string('url', 255);
            $table->string('banner_path', 255)->nullable();
            $table->string('ad_type'); // banner, sponsored, featured, boost
            $table->string('placement'); // header, sidebar, category, inline
            $table->string('status')->default('pending'); // pending, active, expired, rejected
            $table->string('contact_info', 255)->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
