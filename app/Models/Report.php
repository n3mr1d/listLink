<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'link_id',
        'user_id',
        'type',
        'message',
        'status',
    ];

    protected $casts = [
        'type' => Report::class,
    ];
    public function link()
    {
        return $this->belongsTo(Link::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
