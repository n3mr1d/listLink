<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('search_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('query_term')->index();
            $table->foreignId('link_id')->constrained('links')->onDelete('cascade');
            $table->unsignedInteger('click_count')->default(1);
            $table->unsignedInteger('impression_count')->default(1);
            $table->float('ctr')->default(0); // Click-through rate
            $table->timestamp('last_clicked_at')->nullable();
            $table->timestamps();

            $table->unique(['query_term', 'link_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_analytics');
    }
};
