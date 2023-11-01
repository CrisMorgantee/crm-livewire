<?php

use App\Livewire\Auth\Password\Recovery;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;

it('needs to have a route password recovery', function() {
    get(route('auth.password.recovery'))
        ->assertSeeLivewire('auth.password.recovery')
        ->assertOk();
});

it('should be able to request to a password recovery', function() {
    Notification::fake();
    $user = User::factory()->create();

    Livewire::test(Recovery::class)
        ->assertDontSee('You will receive an email with a link to reset your password.')
        ->set('email', $user->email)
        ->call('requestPasswordRecovery')
        ->assertSee('You will receive an email with a link to reset your password.');

    Notification::assertSentTo($user, ResetPassword::class);
});

test('testing email property', function($value, $rule) {
    Livewire::test(Recovery::class)
        ->set('email', $value)
        ->call('requestPasswordRecovery')
        ->assertHasErrors(['email' => $rule]);
})->with([
    'required' => ['value' => '', 'rule' => 'required'],
    'email'    => ['value' => 'invalid', 'rule' => 'email']
]);

test('needs to create token when requesting password recovery', function() {
    $user = User::factory()->create();

    Livewire::test(Recovery::class)
        ->set('email', $user->email)
        ->call('requestPasswordRecovery');

    assertDatabaseCount('password_reset_tokens', 1);
    assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);
});
