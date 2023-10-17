<?php

use App\Livewire\Auth\Register;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

it('should render the component', function() {
    Livewire::test(Register::class)
        ->assertOk();
});

it('should be able to register a new user in the system', function() {
    Livewire::test(Register::class)
        ->set('name', 'Joe Doe')
        ->set('email', 'joe@doe.com')
        ->set('email_confirmation', 'joe@doe.com')
        ->set('password', 'password')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(RouteServiceProvider::HOME);

    assertDatabaseHas('users', [
        'name'  => 'Joe Doe',
        'email' => 'joe@doe.com'
    ]);

    assertDatabaseCount('users', 1);

    expect(auth()->check())
        ->and(auth()->user())
        ->id->toBe(User::first()->id);
});

test('validation rules', function($ctx) {
    if ($ctx->rule == 'unique') {
        User::factory()->create([$ctx->field => $ctx->value]);
    }

    $livewire = Livewire::test(Register::class)
        ->set($ctx->field, $ctx->value);

    if (property_exists($ctx, 'aValue')) {
        $livewire->set($ctx->aField, $ctx->aValue);
    }

    $livewire->call('submit')
        ->assertHasErrors([$ctx->field => $ctx->rule]);
})->with([
    'name::required'     => (object) ['field' => 'name', 'value' => '', 'rule' => 'required'],
    'name::max:255'      => (object) ['field' => 'name', 'value' => str_repeat('*', 256), 'rule' => 'max'],
    'email::required'    => (object) ['field' => 'email', 'value' => '', 'rule' => 'required'],
    'email::email'       => (object) ['field' => 'email', 'value' => 'not-an-email', 'rule' => 'email'],
    'email::confirmed'   => (object) ['field' => 'email', 'value' => 'joe@doe.com', 'rule' => 'confirmed'],
    'email::unique'      => (object) ['field' => 'email', 'value' => 'joe@doe.com', 'rule' => 'unique', 'aField' => 'email_confirmation', 'aValue' => 'joe@doe.com'],
    'email::max:255'     => (object) ['field' => 'email', 'value' => str_repeat('*' . '@doe.com', 256), 'rule' => 'max'],
    'password::required' => (object) ['field' => 'password', 'value' => '', 'rule' => 'required'],
]);

it('should be a notification welcoming the new user', function() {
    Notification::fake();

    Livewire::test(Register::class)
        ->set('name', 'Joe Doe')
        ->set('email', 'joe@doe.com')
        ->set('email_confirmation', 'joe@doe.com')
        ->set('password', 'password')
        ->call('submit');

    $user = User::whereEmail('joe@doe.com')->first();

    Notification::assertSentTo($user, WelcomeNotification::class);
});
