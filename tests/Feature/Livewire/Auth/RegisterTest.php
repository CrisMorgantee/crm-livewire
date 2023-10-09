<?php

use App\Livewire\Auth\Register;
use App\Models\User;
use App\Providers\RouteServiceProvider;
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
        ->set('email_confirmation', 'email_confirmation')
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

test('validation rules', function ($ctx) {
    Livewire::test(Register::class)
        ->set($ctx->field, $ctx->value)
        ->call('submit')
        ->assertHasErrors([$ctx->field => $ctx->rule]);
})->with([
    'name::required' => (object)[ 'field' => 'name', 'value' => '', 'rule' => 'required' ],
    'name::max:255' => (object)[ 'field' => 'name', 'value' => str_repeat('*', 256), 'rule' => 'max' ],
    'email::required' => (object)[ 'field' => 'email', 'value' => '', 'rule' => 'required' ],
    'email::email' => (object)[ 'field' => 'email', 'value' => 'not-an-email', 'rule' => 'email' ],
    'email::confirmed' => (object)[ 'field' => 'email', 'value' => 'joe@doe.com', 'rule' => 'confirmed' ],
    'email::max:255' => (object)[ 'field' => 'email', 'value' => str_repeat('*'.'@doe.com', 256), 'rule' => 'max' ],
    'password::required' => (object)[ 'field' => 'password', 'value' => '', 'rule' => 'required' ],
]);
