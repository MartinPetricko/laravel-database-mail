<?php

declare(strict_types=1);

use MartinPetricko\LaravelDatabaseMail\Properties\Property;

it('can get property resolver', function () {
    $property = (new Property('user'))
        ->accessor('$this->user')
        ->properties([
            (new Property('name'))
                ->accessor('->name'),
        ]);

    expect($property->getAccessor())
        ->toBe('$this->user')
        ->and($property->getProperties()['name']->getAccessor())
        ->toBe('$this->user->name');
});
