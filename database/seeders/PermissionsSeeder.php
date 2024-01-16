<?php

namespace Database\Seeders;

use App\Enum\Can;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        Permission::query()->insert([
            ['name' => Can::BE_AN_ADMIN->value],
            ['name' => Can::BE_AN_MANAGER->value],
        ]);
    }
}
