<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->timestamp('last_crawled_at')->nullable()->after('last_check');
            $table->unsignedInteger('crawl_count')->default(0)->after('last_crawled_at');
            $table->string('crawl_status')->default('pending')->after('crawl_count'); // pending, success, failed
            $table->boolean('force_recrawl')->default(false)->after('crawl_status');
        });
    }

    public function down(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn(['last_crawled_at', 'crawl_count', 'crawl_status', 'force_recrawl']);
        });
    }
};
