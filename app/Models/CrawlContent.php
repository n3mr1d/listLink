<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrawlContent extends Model
{
    protected $table = 'crawl_contents';

    protected $fillable = [
        'link_id',
        'domain',
        'h1',
        'meta_description',
        'body_text',
        'content_type',
        'content_length',
        'language',
    ];

    protected function casts(): array
    {
        return [
            'content_length' => 'integer',
        ];
    }

    /**
     * The parent link this content was crawled from.
     */
    public function link()
    {
        return $this->belongsTo(Link::class);
    }

    /**
     * Scope: search across h1, meta_description, body_text using FULLTEXT.
     */
    public function scopeFullTextSearch($query, string $term)
    {
        return $query->whereRaw(
            'MATCH(h1, meta_description, body_text) AGAINST(? IN BOOLEAN MODE)',
            [$term]
        );
    }
}
