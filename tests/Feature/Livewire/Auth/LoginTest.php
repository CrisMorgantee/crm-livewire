<?php

use App\Livewire\Auth\Login;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Livewire\Livewire;

it('renders successfully', function() {
    Livewire::test(Login::class)
        ->assertOk();
});

it('should be able to login', function() {
    $user = User::factory()->create(['email' => 'joe@doe.com', 'password' => 'password']);

    Livewire::test(Login::class)
        ->set('email', 'joe@doe.com')
        ->set('password', 'password')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(RouteServiceProvider::HOME);

    expect(auth()->check())
        ->toBeTrue()
        ->and(auth()->user())->id->toBe($user->id);
});

it('should make sure inform user that credentials are invalid', function() {
    Livewire::test(Login::class)
        ->set('email', 'joe@doe.com')
        ->set('password', 'invalid')
        ->call('login')
        ->assertHasErrors(['invalidCredentials'])
        ->assertSee(trans('auth.failed'));
});

it('should make sure that the rate limiter is blocking after 5 failed attempts', function() {
    for ($i = 0; $i < 5; $i++) {
        Livewire::test(Login::class)
            ->set('email', 'joe@doe.com')
            ->set('password', 'invalid')
            ->call('login')
            ->assertHasErrors(['invalidCredentials']);
    }

    Livewire::test(Login::class)
        ->set('email', 'joe@doe.com')
        ->set('password', 'invalid')
        ->call('login')
        ->assertHasErrors(['rateLimit']);
});
