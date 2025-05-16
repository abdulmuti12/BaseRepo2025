<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Customer extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'name',
        'full_name',
        'email',
        'phone_number',
        'password',
        'status',
        'time',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
    ];

    // JWTSubject interface methods
    public function getJWTIdentifier()
    {
        return $this->getKey(); // biasanya return id
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
