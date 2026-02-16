<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'link_id',
        'username',
        'content',
    ];

    public function link()
    {
        return $this->belongsTo(Link::class);
    }
}
