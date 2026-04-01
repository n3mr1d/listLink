<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('discovered_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_url_id')
                ->constrained('links')
                ->onDelete('cascade');
            $table->string('url', 2048);
            $table->timestamps();

            // Prevent same url from being recorded twice under the same parent
            $table->unique(['parent_url_id', 'url'], 'unique_parent_discovered');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discovered_links');
    }
};
