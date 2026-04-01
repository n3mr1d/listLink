<?php

namespace App\Console\Commands;

use App\Jobs\CrawlLinkJob;
use App\Models\Link;
use Illuminate\Console\Command;

class DispatchCrawlerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * crawl:dispatch            → Dispatch only links that need crawling (smart mode)
     * crawl:dispatch --all      → Force-crawl every link regardless of history
     * crawl:dispatch --force=5  → Crawl a specific link by ID with force flag
     * crawl:dispatch --batch=50 → Limit how many jobs are dispatched
     * crawl:dispatch --dry-run  → Show what would be dispatched without actually doing it
     */
    protected $signature = 'crawl:dispatch
                            {--all : Crawl all links regardless of crawl history}
                            {--force= : Force-crawl a single link by its ID}
                            {--batch= : Override batch size (0 = all eligible)}
                            {--dry-run : Show what would be dispatched without dispatching}';

    protected $description = 'Dispatch CrawlLinkJob(s) to the queue for pending or overdue links.';

    public function handle(): int
    {
        $interval = config('crawler.interval_days', 4);
        $batchSize = $this->option('batch')
            ? (int) $this->option('batch')
            : config('crawler.batch_size', 0);
        $dryRun = $this->option('dry-run');

        // ── Single forced crawl ───────────────────────────────────────────
        if ($id = $this->option('force')) {
            $link = Link::find((int) $id);
            if (! $link) {
                $this->error("Link #{$id} not found.");
                return self::FAILURE;
            }

            if ($dryRun) {
                $this->info("[DRY RUN] Would force-crawl Link #{$id}: {$link->url}");
                return self::SUCCESS;
            }

            $link->update(['force_recrawl' => true]);
            CrawlLinkJob::dispatch($link->id);
            $this->info("✓ Force-crawl dispatched for Link #{$id}: {$link->url}");
            return self::SUCCESS;
        }

        // ── Crawl ALL links (manual override) ────────────────────────────
        if ($this->option('all')) {
            $query = Link::query();

            if ($batchSize > 0) {
                $query->limit($batchSize);
            }

            $links = $query->get();
            $count = 0;

            foreach ($links as $link) {
                if ($dryRun) {
                    $this->line("  [DRY RUN] #{$link->id} {$link->url}");
                } else {
                    CrawlLinkJob::dispatch($link->id);
                }
                $count++;
            }

            $prefix = $dryRun ? '[DRY RUN] Would dispatch' : '✓ Dispatched';
            $this->info("{$prefix} {$count} jobs to crawl ALL links.");
            return self::SUCCESS;
        }

        // ── Smart mode: crawl only what's needed ─────────────────────────
        $cutoff = now()->subDays($interval);

        $query = Link::where(function ($q) use ($cutoff) {
            $q->whereNull('last_crawled_at')
              ->orWhere('force_recrawl', true)
              ->orWhere('last_crawled_at', '<=', $cutoff);
        });

        if ($batchSize > 0) {
            $query->limit($batchSize);
        }

        $links = $query->get();

        if ($links->isEmpty()) {
            $this->info("No links require crawling at this time (interval: {$interval} days).");
            return self::SUCCESS;
        }

        $count = 0;
        $neverCrawled = 0;
        $overdue = 0;
        $forced = 0;

        foreach ($links as $link) {
            if ($dryRun) {
                $reason = $link->last_crawled_at === null ? 'never' :
                    ($link->force_recrawl ? 'forced' : 'overdue');
                $this->line("  [DRY RUN] #{$link->id} ({$reason}) {$link->url}");
            } else {
                CrawlLinkJob::dispatch($link->id);
            }

            if ($link->last_crawled_at === null) {
                $neverCrawled++;
            } elseif ($link->force_recrawl) {
                $forced++;
            } else {
                $overdue++;
            }

            $count++;
        }

        $prefix = $dryRun ? '[DRY RUN] Would dispatch' : '✓ Dispatched';
        $this->info("{$prefix} {$count} crawl job(s) — smart mode ({$interval}-day interval).");
        $this->line("  ├─ Never crawled: {$neverCrawled}");
        $this->line("  ├─ Force-flagged: {$forced}");
        $this->line("  └─ Overdue:       {$overdue}");

        return self::SUCCESS;
    }
}
