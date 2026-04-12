<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->string('crawl_queue_status', 20)->default('idle')->after('crawl_status');
            $table->timestamp('queued_at')->nullable()->after('crawl_queue_status');
        });
    }

    public function down(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn(['crawl_queue_status', 'queued_at']);
        });
    }
};
