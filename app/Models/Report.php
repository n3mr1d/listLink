<?php

namespace App\Models;

use App\Enum\Report as ReportType;
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
        'type' => ReportType::class,
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
