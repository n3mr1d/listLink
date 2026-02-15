<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->text('description')->default('we dont have a description about this site');
            $table->string('url')->unique();
            $table->string('slug')->unique();
            $table->string('status')->default('active');
            $table->string('category')->default('other');
            $table->dateTime('last_check')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
