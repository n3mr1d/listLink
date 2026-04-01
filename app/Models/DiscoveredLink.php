<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscoveredLink extends Model
{
    protected $table = 'discovered_links';

    protected $fillable = [
        'parent_url_id',
        'url',
    ];

    /**
     * The parent link this URL was discovered from.
     */
    public function parentLink()
    {
        return $this->belongsTo(Link::class, 'parent_url_id');
    }
}
