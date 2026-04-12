<?php
$target = 2903;
$current = \App\Models\Visitor::count();
$needed = $target - $current;
if ($needed > 0) {
    $data = [];
    $now = now();
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
        }
    }
    if (count($data) > 0) {
        \App\Models\Visitor::insert($data);
    }
    echo "Inserted " . $needed . " dummy visitors. Total is now " . \App\Models\Visitor::count() . "\n";
} else {
    echo "Already at or above " . $target . " visitors. Current: " . $current . "\n";
}
