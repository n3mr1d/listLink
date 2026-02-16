<?php

namespace App\Models;

use App\Enum\Category;
use App\Enum\Status;
use App\Enum\UptimeStatus;
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
    ];

    protected function casts(): array
    {
        return [
            'last_check' => 'datetime',
            'category' => Category::class,
            'status' => Status::class,
            'uptime_status' => UptimeStatus::class,
            'is_featured' => 'boolean',
            'check_count' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function uptimeLogs()
    {
        return $this->hasMany(UptimeLog::class)->latest();
    }

    public function scopeActive($query)
    {
        return $query->where('status', Status::ACTIVE);
    }

    public function scopePending($query)
    {
        return $query->where('status', Status::PENDING);
    }

    public function scopeByCategory($query, Category $category)
    {
        return $query->where('category', $category);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'LIKE', "%{$term}%")
                ->orWhere('description', 'LIKE', "%{$term}%")
                ->orWhere('url', 'LIKE', "%{$term}%");
        });
    }
}
