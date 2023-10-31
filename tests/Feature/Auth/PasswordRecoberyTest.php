<?php

use function Pest\Laravel\get;

it('needs to have a route password recovery', function() {
    get(route('auth.password.recovery'))
    ->assertOk();
});
