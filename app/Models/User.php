<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'status',
        'email_verified_at',
        'email_verification_token',
        'email_verification_code',
        'email_verification_sent_at',
    ];

    protected $hidden = [
        'password',
        'email_verification_token',
        'email_verification_code',
    ];

    protected function casts(): array
    {
        return [
            'password'                   => 'hashed',
            'email_verified_at'          => 'datetime',
            'email_verification_sent_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function hasVerifiedEmail(): bool
    {
        return ! is_null($this->email_verified_at);
    }

    public function links()
    {
        return $this->hasMany(Link::class);
    }

    public function advertisements()
    {
        return $this->hasMany(Advertisement::class);
    }
}
