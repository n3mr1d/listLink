<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $fillable = ['session_id', 'ip_address', 'views', 'last_active_at'];

    protected $casts = [
        'last_active_at' => 'datetime',
    ];
