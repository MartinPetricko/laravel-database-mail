<?php

declare(strict_types=1);

use MartinPetricko\LaravelDatabaseMail\Example\Events\Registered;
use MartinPetricko\LaravelDatabaseMail\Properties\Resolvers\ListResolver;

it('can not resolve unspecified array property', function () {
    $class = new class () {
        public array $users;
    };

    $reflectionProperty = new ReflectionProperty($class, 'users');

    expect(ListResolver::canResolve($reflectionProperty))
        ->toBeFalse();
});

it('can resolve simple array property', function () {
    $class = new class () {
        /**
         * @var array<string>
         */
        public array $users;
    };

    $reflectionProperty = new ReflectionProperty($class, 'users');

    expect(ListResolver::canResolve($reflectionProperty))
        ->toBeTrue();

    $arrayProperty = ListResolver::resolve($reflectionProperty);
    expect($arrayProperty->getName())
        ->toBe('users')
        ->and($arrayProperty->isHidden())
        ->toBeFalse()
        ->and($arrayProperty->isNullable())
        ->toBeFalse()
        ->and($arrayProperty->isBoolean())
        ->toBeFalse()
        ->and($arrayProperty->isTraversable())
        ->toBeTrue()
        ->and($arrayProperty->getParent())
        ->toBeNull()
        ->and($arrayProperty->hasProperties())
        ->toBeFalse()
        ->and($arrayProperty->getAccessor())
        ->toBe('$users');
});

it('can resolve eloquent array property', function () {
    $reflectionProperty = new ReflectionProperty(Registered::class, 'users');

    expect(ListResolver::canResolve($reflectionProperty))
        ->toBeTrue();

    $arrayProperty = ListResolver::resolve($reflectionProperty);
    expect($arrayProperty->getName())
        ->toBe('users')
        ->and($arrayProperty->isHidden())
        ->toBeFalse()
        ->and($arrayProperty->isNullable())
        ->toBeFalse()
        ->and($arrayProperty->isBoolean())
        ->toBeFalse()
        ->and($arrayProperty->isTraversable())
        ->toBeTrue()
        ->and($arrayProperty->getParent())
        ->toBeNull()
        ->and($arrayProperty->hasProperties())
        ->toBeTrue()
        ->and($arrayProperty->getProperties())
        ->toHaveKeys(['id', 'name', 'email', 'locale', 'created_at', 'updated_at'])
        ->not->toHaveKeys(['password', 'remember_token'])
        ->and($arrayProperty->getAccessor())
        ->toBe('$users');
});
