<?php

namespace Database\Seeders;

use App\Models\Advertisement;
use App\Models\AdStat;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AdStatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ads = Advertisement::all();

        if ($ads->isEmpty()) {
            $this->command->info('No advertisements found. Skipping AdStatSeeder.');
            return;
        }

        foreach ($ads as $ad) {
            $this->command->info("Seeding stats for ad: {$ad->title}");
            
            // Seed for the last 365 days to cover the 12-month chart
            for ($i = 365; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->toDateString();
                
                // Random but somewhat realistic trends
                // Higher impressions on weekends
                $isWeekend = Carbon::parse($date)->isWeekend();
                $baseImp   = $isWeekend ? rand(1500, 3000) : rand(800, 1800);
                $baseClick = $isWeekend ? rand(80, 200) : rand(30, 90);

                AdStat::updateOrCreate(
                    [
                        'advertisement_id' => $ad->id,
                        'date' => $date,
                    ],
                    [
                        'impressions' => $baseImp,
                        'clicks'      => $baseClick,
                    ]
                );
            }
        }

        $this->command->info('AdStatSeeder completed successfully.');
    }
}
