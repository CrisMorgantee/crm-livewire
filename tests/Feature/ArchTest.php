<?php

test('global', function() {
    expect(['dd', 'dump', 'ray', 'ds'])
        ->not()->toBeUsed();
});
