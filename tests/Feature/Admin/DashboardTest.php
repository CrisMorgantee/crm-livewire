<?php

use App\Livewire\Admin\Dashboard;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('should block access to admin pages if the user does not have the permission to be an admin', function() {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden();

    Livewire::test(Dashboard::class)
        ->assertForbidden();
});
