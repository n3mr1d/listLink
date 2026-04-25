<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->unsignedInteger('likes_count')->default(0)->after('is_featured');
            $table->unsignedInteger('dislikes_count')->default(0)->after('likes_count');
            $table->timestamp('last_voted_at')->nullable()->after('dislikes_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('links', function (Blueprint $table) {
            //
        });
    }
};
