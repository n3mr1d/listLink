<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── 1. Crawl Content — full-text indexed page content ────────────
        Schema::create('crawl_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_id')
                ->constrained('links')
                ->onDelete('cascade');

            $table->string('domain', 255)->nullable()->index();
            $table->string('h1', 500)->nullable();
            $table->text('meta_description')->nullable();
            $table->mediumText('body_text')->nullable();      // plain-text content
            $table->string('content_type', 100)->nullable();
            $table->unsignedInteger('content_length')->default(0);
            $table->string('language', 10)->nullable();

            $table->timestamps();

            // One content record per link (updated on each crawl)
            $table->unique('link_id');
        });

        // ── 2. Crawl Logs — audit trail per-crawl attempt ────────────────
        Schema::create('crawl_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_id')
                ->constrained('links')
                ->onDelete('cascade');

            $table->string('status', 20);   // success, failed, timeout, skipped
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->string('error_message', 1000)->nullable();
            $table->unsignedInteger('response_time_ms')->nullable();
            $table->unsignedInteger('discovered_count')->default(0);
            $table->unsignedInteger('content_length')->default(0);

            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['link_id', 'created_at']);
            $table->index('status');
        });

        // ── 3. Add full-text index to crawl_contents ─────────────────────
        // MySQL FULLTEXT for optimized search (Ahmia-index inspired)
        Schema::table('crawl_contents', function (Blueprint $table) {
            $table->fullText(['h1', 'meta_description', 'body_text'], 'ft_crawl_content');
        });

        // ── 4. Add full-text index to links table (title + description) ──
        Schema::table('links', function (Blueprint $table) {
            $table->fullText(['title', 'description'], 'ft_links_search');
        });

        // ── 5. Add index on crawler fields for faster queries ────────────
        Schema::table('links', function (Blueprint $table) {
            $table->index('crawl_status');
            $table->index('last_crawled_at');
            $table->index('force_recrawl');
        });
    }

    public function down(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->dropIndex('links_crawl_status_index');
            $table->dropIndex('links_last_crawled_at_index');
            $table->dropIndex('links_force_recrawl_index');
            $table->dropFullText('ft_links_search');
        });

        Schema::dropIfExists('crawl_logs');
        Schema::dropIfExists('crawl_contents');
    }
};
