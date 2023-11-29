<?php

use App\Models\Permission;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\UsersSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\seed;

it('should be able to give an user a permission to do something', function() {
    /** @var User $user */
    $user = User::factory()->create();

    $user->givePermissionTo('doSomething');

    expect($user)
        ->hasPermissionTo('doSomething')
        ->toBeTrue();

    assertDatabaseHas('permissions', [
        'name' => 'doSomething',
    ]);

    assertDatabaseHas('permission_user', [
        'user_id'       => $user->id,
        'permission_id' => Permission::query()->whereName('doSomething')->first()->id, ]);
});

test('permission has to have a seeder', function() {
    seed(PermissionsSeeder::class);

    assertDatabaseHas('permissions', [
        'name' => 'be an admin',
    ]);
});

test('seed with an admin user', function() {
    seed([PermissionsSeeder::class, UsersSeeder::class]);

    assertDatabaseHas('permissions', [
        'name' => 'be an admin',
    ]);

    assertDatabaseHas('permission_user', [
        'user_id'       => User::first()->id,
        'permission_id' => Permission::query()->whereName('be an admin')->first()->id,
    ]);
});

test('should block the access to admin pages if the user does not have the permission to be an admin', function() {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

test('should allow the access to admin pages if the user has the permission to be an admin', function() {
    $user = User::factory()->create();

    $user->givePermissionTo('be an admin');

    actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertOk();
});
