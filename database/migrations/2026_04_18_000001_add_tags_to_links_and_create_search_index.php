<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Add tags column to links ──────────────────────────────────
        Schema::table('links', function (Blueprint $table) {
            // Comma-separated tags for weighted search scoring
            $table->string('tags', 1000)->nullable()->after('description');
        });

        // ── 2. Inverted search index ─────────────────────────────────────
        // Stores per-term → per-document TF-IDF statistics.
        // Populated/updated by the SearchIndexService on link create/update.
        Schema::create('search_index', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_id')->constrained('links')->onDelete('cascade');

            // The stemmed, normalised token
            $table->string('term', 100)->index();

            // Cumulative raw counts per field (used to compute TF)
            $table->unsignedSmallInteger('title_count')->default(0);
            $table->unsignedSmallInteger('tag_count')->default(0);
            $table->unsignedSmallInteger('description_count')->default(0);
            $table->unsignedSmallInteger('body_count')->default(0);

            // Pre-computed weighted TF-IDF score (updated when IDF changes)
            $table->float('tf_idf_score', 8, 4)->default(0);

            $table->timestamps();

            // Each link should only have one row per term
            $table->unique(['link_id', 'term']);
        });

        // ── 3. IDF cache ─────────────────────────────────────────────────
        // Stores the document frequency of each term for fast IDF lookup.
        Schema::create('search_idf', function (Blueprint $table) {
            $table->id();
            $table->string('term', 100)->unique();
            // Number of documents containing this term
            $table->unsignedInteger('doc_frequency')->default(1);
            // Total documents at last IDF computation
            $table->unsignedInteger('total_docs')->default(1);
            // Cached IDF value = log(total_docs / doc_frequency)
            $table->float('idf_value', 8, 4)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_idf');
        Schema::dropIfExists('search_index');

        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn('tags');
        });
    }
};
