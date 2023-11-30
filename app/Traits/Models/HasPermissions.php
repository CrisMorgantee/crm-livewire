<?php

namespace App\Traits\Models;

use App\Enum\Can;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

trait HasPermissions
{
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function givePermissionTo(Can|string $name): void
    {
        if ($name instanceof Can) {
            $name = $name->value;
        }

        $this->permissions()->firstOrCreate(['name' => $name]);

        Cache::forget($this->getPermissionCacheKey());

        Cache::rememberForever($this->getPermissionCacheKey(), fn() => $this->permissions);
    }

    public function hasPermissionTo(Can|string $name): bool
    {
        if ($name instanceof Can) {
            $name = $name->value;
        }

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
