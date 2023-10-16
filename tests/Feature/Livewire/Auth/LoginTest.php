<?php

use App\Livewire\Auth\Login;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Livewire\Livewire;
use function Pest\Laravel\actingAs;

it('renders successfully', function () {
    Livewire::test(Login::class)
        ->assertOk();
});

it('should be able to login', function () {
    $user = User::factory()->create(['email' => 'joe@doe.com', 'password' => 'password']);

    Livewire::test(Login::class)
        ->set('email', 'joe@doe.com')
        ->set('password', 'password')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(RouteServiceProvider::HOME);

    actingAs($user)->assertAuthenticated();

    expect(auth()->check())
        ->toBeTrue()
        ->end(auth()->user())->id->toBe($user->id);
});
