<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crawled_emails', function (Blueprint $table) {
            $table->id();
            $table->string('email', 320)->unique();                // validated, deduplicated
            $table->string('source_url', 2048)->nullable();        // where it was found
            $table->string('source_domain', 255)->nullable();      // normalized domain
            $table->string('page_title', 500)->nullable();         // page title at source
            $table->enum('status', ['active', 'invalid', 'unsubscribed'])->default('active');
            $table->enum('source_type', ['auto_crawl', 'manual'])->default('auto_crawl');
            $table->string('crawl_job_id', 64)->nullable()->index(); // batch tracking
            $table->boolean('exported')->default(false);
            $table->timestamp('first_seen_at')->useCurrent();
            $table->timestamp('last_seen_at')->useCurrent();
            $table->timestamps();

            $table->index('source_domain');
            $table->index('status');
            $table->index('source_type');
            $table->index('exported');
            $table->index('first_seen_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crawled_emails');
    }
};
