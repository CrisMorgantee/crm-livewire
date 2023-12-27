<?php

use App\Enum\Can;
use App\Livewire\Admin\Users\Index;
use App\Models\Permission;
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

test("let's create a livewire component to list all users in the page", function() {
    $users = User::factory()->count(10)->create();

    $lw = Livewire::test(Index::class)
        ->assertSet('users', function($usr) use ($users) {
            return $usr->count() === $users->count();
        });

    foreach ($users as $user) {
        $lw->assertSee($user->name);
    }
});

it('should be able to filter users by name or email', function() {
    $admin = User::factory()->admin()->create([
        'name'  => 'Joe Doe',
        'email' => 'admin@example.com'
    ]);

    $user = User::factory()->create([
        'name'  => 'Mary Doe',
        'email' => 'user2@obs.com'
    ]);

    actingAs($admin);

    Livewire::test(Index::class)
        ->assertSet('users', function($usr) {
            return $usr->count() === 2;
        })
        ->set('search', 'mary')
        ->assertSet('users', function($usr) use ($user) {
            return $usr->count() === 1 && $usr->first()->is($user);
        });
});

it('should be able to filter users by permissions key', function() {
    $admin = User::factory()->admin()->create([
        'name'  => 'Joe Doe',
        'email' => 'admin@example.com'
    ]);

    $manager = User::factory()->withPermission(Can::BE_AN_MANAGER)->create([
        'name'  => 'Mary Doe',
        'email' => 'user2@obs.com'
    ]);

    $be_an_admin   = Permission::whereName(Can::BE_AN_ADMIN->value)->first();
    $be_an_manager = Permission::whereName(Can::BE_AN_MANAGER->value)->first();

    actingAs($admin);

    Livewire::test(Index::class)
        ->assertSet('users', function($usr) {
            expect($usr)->toHaveCount(2);

            return true;
        })
        ->set('search_permissions', [$be_an_manager->id, $be_an_admin->id])
        ->assertSet('users', function($usr) {
            expect($usr)->toHaveCount(2);

            return true;
        });
});

it('should be able to list deleted users', function() {
    $admin = User::factory()->admin()->create([
        'name'  => 'Joe Doe',
        'email' => 'admin@example.com'
    ]);

    $deletedUsers = User::factory()->count(2)->create([
        'deleted_at' => now(),
    ]);

    actingAs($admin);

    Livewire::test(Index::class)
        ->assertSet('users', function($usr) {
            expect($usr)->toHaveCount(1);

            return true;
        })
        ->set('search_trash', true)
        ->assertSet('users', function($usr) {
            expect($usr)->toHaveCount(2);

            return true;
        });
});
