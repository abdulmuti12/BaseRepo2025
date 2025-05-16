<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'admin_role');
    }

    public function hasAccessToMenu(string $menu): bool
    {
        return $this->roles()->whereHas('menus', function ($q) use ($menu) {
            $q->where('route', $menu);
        })->exists();
    }

    public function getJWTIdentifier()
    {
        return $this->getKey(); // biasanya ID dari admin
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

}
