<?php

use App\Livewire\Auth\Password;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;

use function Pest\Laravel\get;
use function PHPUnit\Framework\assertTrue;

test('need to receive a valid token  whit a combination with the email', function() {
    Notification::fake();
    $user = User::factory()->create();

    Livewire::test(Password\Recovery::class)
        ->set('email', $user->email)
        ->call('requestPasswordRecovery');

    $passwordResetToken = DB::table('password_reset_tokens')
        ->where('email', $user->email)
        ->first();

    Notification::assertSentTo(
        $user,
        ResetPassword::class,
        function(ResetPassword $notification) {
            get(route('password.reset') . '?token=' . $notification->token)
                ->assertSuccessful();

            get(route('password.reset') . '?token=invalid-token')
                ->assertRedirect(route('login'));

            return true;
        }
    );
});

test('test if is possible to reset the password with the given token', function() {
    Notification::fake();
    $user = User::factory()->create();

    Livewire::test(Password\Recovery::class)
        ->set('email', $user->email)
        ->call('requestPasswordRecovery');

    Notification::assertSentTo(
        $user,
        ResetPassword::class,
        function(ResetPassword $notification) use ($user) {
            Livewire::test(
                Password\Reset::class,
                ['token' => $notification->token, 'email' => $user->email],
            )
                ->set('email_confirmation', $user->email)
                ->set('password', 'new-password')
                ->set('password_confirmation', 'new-password')
                ->call('updatePassword')
                ->assertHasNoErrors()
                ->assertRedirect(route('dashboard'));

            $user->refresh();

            assertTrue(Hash::check('new-password', $user->password));

            return true;
        }
    );
});

test('check form rules', function($field, $value, $rule) {
    Notification::fake();
    $user = User::factory()->create();

    Livewire::test(Password\Recovery::class)
        ->set('email', $user->email)
        ->call('requestPasswordRecovery');

    Notification::assertSentTo(
        $user,
        ResetPassword::class,
        function(ResetPassword $notification) use ($user, $field, $value, $rule) {
            Livewire::test(Password\Reset::class, ['token' => $notification->token, 'email' => $user->email])
                ->set($field, $value)
                ->call('updatePassword')
                ->assertHasErrors([$field => $rule]);

            return true;
        }
    );
})->with([
    'email:required'     => ['field' => 'email', 'value' => '', 'rule' => 'required'],
    'email:email'        => ['field' => 'email', 'value' => 'invalid-mail', 'rule' => 'email'],
    'password:required'  => ['field' => 'password', 'value' => '', 'rule' => 'required'],
    'password:confirmed' => ['field' => 'password', 'value' => 'password', 'rule' => 'confirmed'],
]);
