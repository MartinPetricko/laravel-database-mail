<?php

declare(strict_types=1);

use MartinPetricko\LaravelDatabaseMail\Example\Events\Registered;
use MartinPetricko\LaravelDatabaseMail\Facades\LaravelDatabaseMail;

it('can get registered events', function () {
    config()?->set('database-mail.events', [
        Registered::class,
    ]);

    expect(LaravelDatabaseMail::getEvents())
        ->toBe([
            Registered::class,
        ]);
});

it('can get event attributes', function () {
    $attributes = LaravelDatabaseMail::getEventAttributes(Registered::class);

    expect($attributes)
        ->toBeArray()
        ->toHaveKeys(['user', 'additionalData']);
});
