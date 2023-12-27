<?php

use App\Enum\Can;
use App\Models\Permission;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Database\Seeders\UsersSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\seed;

it('should be able to give an user a permission to do something', function() {
    /** @var User $user */
    $user = User::factory()->admin()->create();

    expect($user)
        ->hasPermissionTo(Can::BE_AN_ADMIN)
        ->toBeTrue();

    assertDatabaseHas('permissions', [
        'name' => Can::BE_AN_ADMIN->value,
    ]);

    assertDatabaseHas('permission_user', [
        'user_id'       => $user->id,
        'permission_id' => Permission::query()->whereName(Can::BE_AN_ADMIN->value)->first()->id,
    ]);
});

test('permission must have a seeder', function() {
    seed(PermissionsSeeder::class);

    assertDatabaseHas('permissions', [
        'name' => Can::BE_AN_ADMIN->value,
    ]);
});

test('seed with an admin user', function() {
    seed([PermissionsSeeder::class, UsersSeeder::class]);

    assertDatabaseHas('permissions', [
        'name' => Can::BE_AN_ADMIN->value,
    ]);

    assertDatabaseHas('permission_user', [
        'user_id'       => User::first()->id,
        'permission_id' => Permission::query()->whereName(Can::BE_AN_ADMIN->value)->first()->id,
    ]);
});

test('should block the access to admin pages if the user does not have the permission to be an admin', function() {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

test('should allow the access to admin pages if the user has the permission to be an admin', function() {
    $user = User::factory()->admin()->create();

    actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertOk();
});

test("let's make sure that we are using cache to store user permissions", function() {
    $user = User::factory()->admin()->create();

    $cacheKey = "user::{$user->id}::permissions";

    expect(Cache::has($cacheKey))->toBeTrue('The cache key should exist')
        ->and(Cache::get($cacheKey))->toBe($user->permissions, 'The cache should contain the user permissions');
});

test("let's make sure that we are using cache to retrieve/check when the user has the given permission", function() {
    $user = User::factory()->admin()->create();

    DB::listen(fn($query) => throw new Exception('The query should not be executed'));
    $user->hasPermissionTo(Can::BE_AN_ADMIN);

    expect(true)->toBeTrue();
});
