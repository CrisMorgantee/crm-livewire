<?php

use App\Livewire\Admin\Users\Index;
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
