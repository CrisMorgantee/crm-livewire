<?php

use App\Models\Permission;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;

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
        'permission_id' => Permission::whereName('doSomething')->first()->id, ]);
});
