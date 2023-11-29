<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

trait HasPermissions
{
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
