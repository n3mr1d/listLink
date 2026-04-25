<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = [
        'link_id',
        'user_id',
        'ip_address',
        'user_agent',
        'fingerprint',
        'is_dislike',
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
