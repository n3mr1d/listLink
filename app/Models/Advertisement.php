<?php

namespace App\Models;

use App\Enum\AdType;
use App\Enum\AdPlacement;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'url',
        'banner_path',
        'ad_type',
        'placement',
        'status',
        'contact_info',
        'starts_at',
        'expires_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'ad_type' => AdType::class,
            'placement' => AdPlacement::class,
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeByPlacement($query, AdPlacement $placement)
    {
        return $query->where('placement', $placement);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
