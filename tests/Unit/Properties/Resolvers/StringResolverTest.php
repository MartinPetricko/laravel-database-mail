<?php

declare(strict_types=1);

use MartinPetricko\LaravelDatabaseMail\Properties\Resolvers\StringResolver;

it('can resolve string property', function () {
    $class = new class () {
        public string $string;

        public ?int $nullableInt;
    };

    $stringReflectionProperty = new ReflectionProperty($class, 'string');
    $nullableIntReflectionProperty = new ReflectionProperty($class, 'nullableInt');

    expect(StringResolver::canResolve($stringReflectionProperty))
        ->toBeTrue()
        ->and(StringResolver::canResolve($nullableIntReflectionProperty))
        ->toBeTrue();

    $stringProperty = StringResolver::resolve($stringReflectionProperty);
    expect($stringProperty->getName())
        ->toBe('string')
        ->and($stringProperty->isNullable())
        ->toBeFalse()
        ->and($stringProperty->isBoolean())
        ->toBeFalse()
        ->and($stringProperty->isTraversable())
        ->toBeFalse()
        ->and($stringProperty->getParent())
        ->toBeNull()
        ->and($stringProperty->getProperties())
        ->toBeEmpty()
        ->and($stringProperty->hasProperties())
        ->toBeFalse()
        ->and($stringProperty->getAccessor())
        ->toBe('$string');

    $nullableIntProperty = StringResolver::resolve($nullableIntReflectionProperty);
    expect($nullableIntProperty->getName())
        ->toBe('nullableInt')
        ->and($nullableIntProperty->isNullable())
        ->toBeTrue()
        ->and($nullableIntProperty->isBoolean())
        ->toBeFalse()
        ->and($nullableIntProperty->isTraversable())
        ->toBeFalse()
        ->and($nullableIntProperty->getParent())
        ->toBeNull()
        ->and($nullableIntProperty->getProperties())
        ->toBeEmpty()
        ->and($nullableIntProperty->hasProperties())
        ->toBeFalse()
        ->and($nullableIntProperty->getAccessor())
        ->toBe('$nullableInt');
});
