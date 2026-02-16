<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UptimeLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'link_id',
        'checked_by_ip_hash',
        'status',
        'response_time_ms',
        'checked_at',
    ];

    protected function casts(): array
    {
        return [
            'checked_at' => 'datetime',
            'response_time_ms' => 'integer',
        ];
    }

    public function link()
    {
        return $this->belongsTo(Link::class);
    }
}
