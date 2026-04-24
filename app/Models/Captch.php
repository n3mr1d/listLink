<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Captch extends Model
{
    protected $fillable = ['code', 'expired_at'];
    protected $casts = [
        'expired_at' => 'datetime',
    ];
    public function checkExpired(): bool
    {
        if ($this->expired_at < now()) {
            return false;
        }
        return true;
    }
    public function checkCode($code): bool
    {
        if ($this->code == $code) {
            return true;
        }
        return false;
    }

}
