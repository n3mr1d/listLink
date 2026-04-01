<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Crawler Scheduler
|--------------------------------------------------------------------------
| The smart dispatcher runs every 6 hours but only crawls links that are
| overdue (last crawled > 4 days ago), never crawled, or force-flagged.
| The 6-hour check cadence ensures new/forced links are picked up quickly,
| while the 4-day interval logic inside DispatchCrawlerCommand prevents
| redundant re-crawls.
|
| Requires: php artisan schedule:work (local) or system cron (production)
| Cron:     * * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
*/
Schedule::command('crawl:dispatch')
    ->everySixHours()
    ->name('crawler-smart-dispatch')
    ->withoutOverlapping(60) // Lock for 60 minutes max
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/crawler-schedule.log'));
