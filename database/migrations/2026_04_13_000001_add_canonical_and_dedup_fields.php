<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('links', function (Blueprint $table) {
            // Canonical URL: the normalized, deduplicated form of this link's URL
            $table->string('canonical_url', 2048)->nullable()->after('url');
            // If this link is a duplicate, points to the canonical link's ID
            $table->unsignedBigInteger('canonical_id')->nullable()->after('canonical_url');
            // Content hash for similarity detection (md5 of normalized body text)
            $table->string('content_hash', 64)->nullable()->after('canonical_id');
            // Whether this link has been marked as duplicate
            $table->boolean('is_duplicate')->default(false)->after('content_hash');

            $table->index('canonical_url');
            $table->index('content_hash');
            $table->index('is_duplicate');
            $table->foreign('canonical_id')->references('id')->on('links')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->dropForeign(['canonical_id']);
            $table->dropIndex(['canonical_url']);
            $table->dropIndex(['content_hash']);
            $table->dropIndex(['is_duplicate']);
            $table->dropColumn(['canonical_url', 'canonical_id', 'content_hash', 'is_duplicate']);
        });
    }
};
