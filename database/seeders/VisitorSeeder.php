<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VisitorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $target = 2903;
        $current = \App\Models\Visitor::count();
        $needed = $target - $current;

        if ($needed > 0) {
            $data = [];
            $now = now();
            
            $this->command->info("Generating {$needed} dummy visitors...");
            $bar = $this->command->getOutput()->createProgressBar($needed);

            for ($i = 0; $i < $needed; $i++) {
                $data[] = [
                    'session_id' => uniqid('dummy_') . rand(1000, 9999),
                    'ip_address' => '127.0.0.1',
                    'views' => 1,
                    'last_active_at' => $now->copy()->subMinutes(rand(10, 60000)),
                    'created_at' => clone $now,
                    'updated_at' => clone $now,
                ];
                
                if (count($data) >= 500) {
                    \App\Models\Visitor::insert($data);
                    $data = [];
                    $bar->advance(500);
                }
            }
            
            if (count($data) > 0) {
                \App\Models\Visitor::insert($data);
                $bar->advance(count($data));
            }

            $bar->finish();
            $this->command->info("\nDatabase seeded! Total visitors now: " . \App\Models\Visitor::count());
        } else {
            $this->command->info("Already at or above {$target} visitors. Current: {$current}");
        }
    }
}
