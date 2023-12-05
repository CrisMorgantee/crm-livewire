<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        Permission::query()->insert([
            'name' => 'be an admin',
        ]);
    }
}
