<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()
            ->withPermission('be an admin')
            ->create([
                'name'  => 'Test User',
                'email' => 'test@example.com',
            ]);
    }
}
