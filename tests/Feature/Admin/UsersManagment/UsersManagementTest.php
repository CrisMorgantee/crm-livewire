<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

it('should be able to access the route admin/users', function() {
    $user = User::factory()->admin()->create();

    actingAs($user)
        ->get(route('admin.users'))
        ->assertOk();
});

test('make sure that the route is protected by the permission BE_AN_ADMIN', function() {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('admin.users'))
        ->assertForbidden();
});
