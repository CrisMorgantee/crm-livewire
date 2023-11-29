<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function givePermissionTo(string $name): void
    {
        $this->permissions()->firstOrCreate(compact('name'));

        Cache::forget($this->getPermissionCacheKey());

        Cache::rememberForever($this->getPermissionCacheKey(), fn() => $this->permissions);
    }

    public function hasPermissionTo(string $name): bool
    {
        $permissions = Cache::get($this->getPermissionCacheKey(), $this->permissions);

        return $permissions->contains('name', $name);
    }

    /**
     * @return string
     */
    private function getPermissionCacheKey(): string
    {
        return "user::{$this->id}::permissions";
    }
}
