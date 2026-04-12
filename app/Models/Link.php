<?php

namespace App\Models;

use App\Enum\Category;
use App\Enum\Status;
use App\Enum\UptimeStatus;
use App\Models\CrawlContent;
use App\Models\CrawlLog;
use App\Models\DiscoveredLink;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $table = 'links';

    protected $fillable = [
        'title',
        'description',
        'url',
        'slug',
        'status',
        'category',
        'uptime_status',
        'last_check',
        'check_count',
        'is_featured',
        'user_id',
        // Crawler fields
        'last_crawled_at',
        'crawl_count',
        'crawl_status',
        'crawl_queue_status',
        'queued_at',
        'force_recrawl',
        // Canonical & dedup fields
        'canonical_url',
        'canonical_id',
        'content_hash',
        'is_duplicate',
    ];

    protected function casts(): array
    {
        return [
            'last_check'      => 'datetime',
            'last_crawled_at' => 'datetime',
            'queued_at'       => 'datetime',
            'category'        => Category::class,
            'status'          => Status::class,
            'uptime_status'   => UptimeStatus::class,
            'is_featured'     => 'boolean',
            'force_recrawl'   => 'boolean',
            'is_duplicate'    => 'boolean',
            'check_count'     => 'integer',
            'crawl_count'     => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The canonical (primary) link this one is a duplicate of.
     */
    public function canonicalLink()
    {
        return $this->belongsTo(Link::class, 'canonical_id');
    }

    /**
     * All links that are duplicates of this one.
     */
    public function duplicates()
    {
        return $this->hasMany(Link::class, 'canonical_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function uptimeLogs()
    {
        return $this->hasMany(UptimeLog::class)->latest();
    }

    public function discoveredLinks()
    {
        return $this->hasMany(DiscoveredLink::class, 'parent_url_id');
    }

    /**
     * Crawled page content (one-to-one).
     */
    public function crawlContent()
    {
        return $this->hasOne(CrawlContent::class);
    }

    /**
     * Crawl audit logs (one-to-many).
     */
    public function crawlLogs()
    {
        return $this->hasMany(CrawlLog::class)->latest();
    }

    /**
     * Extract the domain from the link URL.
     */
    public function getDomainAttribute(): ?string
    {
        return parse_url($this->url, PHP_URL_HOST);
    }

    /**
     * Scope: links that need crawling (never crawled, force flagged, or overdue).
     * Uses the configurable interval from config/crawler.php.
     */
    public function scopeNeedsCrawling($query)
    {
        $interval = now()->subDays(config('crawler.interval_days', 4));

        return $query->where(function ($q) use ($interval) {
            $q->whereNull('last_crawled_at')
              ->orWhere('force_recrawl', true)
              ->orWhere('last_crawled_at', '<=', $interval);
        });
    }

    /**
     * Scope: FULLTEXT search on links title + description.
     * Falls back to LIKE if query is too short for FULLTEXT.
     */
    public function scopeFullTextSearch($query, string $term)
    {
        // MySQL FULLTEXT requires 3+ chars in boolean mode for meaningful results
        if (mb_strlen($term) >= 3) {
            return $query->whereRaw(
                'MATCH(title, description) AGAINST(? IN BOOLEAN MODE)',
                [$term]
            );
        }

        // Fallback for very short queries
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'LIKE', "%{$term}%")
              ->orWhere('description', 'LIKE', "%{$term}%")
              ->orWhere('url', 'LIKE', "%{$term}%");
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', Status::ACTIVE)
            ->where('is_duplicate', false);
    }

    /**
     * Scope: only non-duplicate links.
     */
    public function scopeNotDuplicate($query)
    {
        return $query->where('is_duplicate', false);
    }

    /**
     * Scope: only duplicate links.
     */
    public function scopeIsDuplicate($query)
    {
        return $query->where('is_duplicate', true);
    }

    public function scopePending($query)
    {
        return $query->where('status', Status::PENDING);
    }

    public function scopeOnline($query)
    {
        return $query->where('uptime_status', UptimeStatus::ONLINE);
    }

    public function scopeByCategory($query, Category $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Legacy LIKE-based search scope (kept for backward compat).
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'LIKE', "%{$term}%")
                ->orWhere('description', 'LIKE', "%{$term}%")
                ->orWhere('url', 'LIKE', "%{$term}%")
                ->orWhereHas('crawlContent', function ($sub) use ($term) {
                    $sub->where('h1', 'LIKE', "%{$term}%")
                        ->orWhere('meta_description', 'LIKE', "%{$term}%")
                        ->orWhere('body_text', 'LIKE', "%{$term}%");
                })
                ->orWhereHas('discoveredLinks', function ($sub) use ($term) {
                    $sub->where('url', 'LIKE', "%{$term}%");
                });
        });
    }
}
