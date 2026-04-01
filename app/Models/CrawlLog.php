<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrawlLog extends Model
{
    protected $table = 'crawl_logs';

    protected $fillable = [
        'link_id',
        'status',
        'http_status',
        'error_message',
        'response_time_ms',
        'discovered_count',
        'content_length',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'http_status'      => 'integer',
            'response_time_ms' => 'integer',
            'discovered_count' => 'integer',
            'content_length'   => 'integer',
            'started_at'       => 'datetime',
            'finished_at'      => 'datetime',
        ];
    }

    /**
     * The link this log entry belongs to.
     */
    public function link()
    {
        return $this->belongsTo(Link::class);
    }

    /**
     * Scope: filter by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Duration in seconds.
     */
    public function getDurationAttribute(): ?float
    {
        if ($this->started_at && $this->finished_at) {
            return $this->finished_at->diffInMilliseconds($this->started_at) / 1000;
        }
        return null;
    }
}
