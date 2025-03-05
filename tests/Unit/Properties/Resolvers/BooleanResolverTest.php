<?php

declare(strict_types=1);

use MartinPetricko\LaravelDatabaseMail\Properties\Resolvers\BooleanResolver;

it('can resolve boolean property', function () {
    $class = new class () {
        public bool $boolean;

        public ?bool $nullableBoolean;
    };

    $booleanReflectionProperty = new ReflectionProperty($class, 'boolean');
    $nullableBooleanReflectionProperty = new ReflectionProperty($class, 'nullableBoolean');

    expect(BooleanResolver::canResolve($booleanReflectionProperty))
        ->toBeTrue()
        ->and(BooleanResolver::canResolve($nullableBooleanReflectionProperty))
        ->toBeTrue();

    $booleanProperty = BooleanResolver::resolve($booleanReflectionProperty);
    expect($booleanProperty->getName())
        ->toBe('boolean')
        ->and($booleanProperty->isNullable())
        ->toBeFalse()
        ->and($booleanProperty->isBoolean())
        ->toBeTrue()
        ->and($booleanProperty->isTraversable())
        ->toBeFalse()
        ->and($booleanProperty->getParent())
        ->toBeNull()
        ->and($booleanProperty->getProperties())
        ->toBeEmpty()
        ->and($booleanProperty->hasProperties())
        ->toBeFalse()
        ->and($booleanProperty->getAccessor())
        ->toBe('$boolean');

    $nullableBooleanProperty = BooleanResolver::resolve($nullableBooleanReflectionProperty);
    expect($nullableBooleanProperty->getName())
        ->toBe('nullableBoolean')
        ->and($nullableBooleanProperty->isNullable())
        ->toBeTrue()
        ->and($nullableBooleanProperty->isBoolean())
        ->toBeTrue()
        ->and($nullableBooleanProperty->isTraversable())
        ->toBeFalse()
        ->and($nullableBooleanProperty->getParent())
        ->toBeNull()
        ->and($nullableBooleanProperty->getProperties())
        ->toBeEmpty()
        ->and($nullableBooleanProperty->hasProperties())
        ->toBeFalse()
        ->and($nullableBooleanProperty->getAccessor())
        ->toBe('$nullableBoolean');
});
