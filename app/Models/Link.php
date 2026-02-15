<?php

namespace App\Models;

use App\Enum\Category;
use App\Enum\Status;
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
        'last_check',
    ];
    protected function casts(): array
    {
        return [
            'last_check' => 'datetime',
            'category' =>  Category::class,
            'status' => Status::class,
        ];
    }
}
