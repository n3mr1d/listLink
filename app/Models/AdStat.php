<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdStat extends Model
{
    protected $fillable = [
        'advertisement_id',
        'date',
        'impressions',
        'clicks',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }
}
