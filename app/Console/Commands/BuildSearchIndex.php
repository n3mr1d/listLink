<?php

namespace App\Console\Commands;

use App\Services\SearchEngineService;
use App\Services\SearchIndexService;
use Illuminate\Console\Command;

class BuildSearchIndex extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'search:index
                            {--rebuild : Truncate and rebuild the entire index from scratch}
                            {--missing : Only index links not yet in the index}';

    /**
     * The console command description.
     */
    protected $description = 'Build or rebuild the inverted search index for the intelligent search engine.';

    public function handle(SearchIndexService $indexService): int
    {
        $mode = $this->option('rebuild') ? 'rebuild' : ($this->option('missing') ? 'missing' : 'rebuild');

        if ($mode === 'rebuild') {
            $this->info('Rebuilding full search index…');
        } else {
            $this->info('Indexing missing links…');
        }

        $bar     = null;
        $lastPct = -1;

        $progress = function (int $current, int $total) use (&$bar, &$lastPct) {
            if ($bar === null && $total > 0) {
                $bar = $this->output->createProgressBar($total);
                $bar->setFormat('[%bar%] %current%/%max% (%percent:3s%%) - Elapsed: %elapsed:6s%');
                $bar->start();
            }
            if ($bar) {
                $bar->setProgress($current);
            }
        };

        $done = match ($mode) {
            'rebuild' => $indexService->rebuildAll($progress),
            'missing' => $indexService->indexMissing($progress),
        };

        if ($bar) {
            $bar->finish();
            $this->newLine();
        }

        $this->info("✓ Indexed {$done} link(s) successfully.");

        return Command::SUCCESS;
    }
}
