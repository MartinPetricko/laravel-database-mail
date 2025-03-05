<?php

declare(strict_types=1);

use MartinPetricko\LaravelDatabaseMail\Example\Models\User;
use MartinPetricko\LaravelDatabaseMail\Properties\Resolvers\EloquentResolver;

it('can resolve boolean property', function () {
    $class = new class () {
        public ?User $user;
    };

    $reflectionProperty = new ReflectionProperty($class, 'user');

    expect(EloquentResolver::canResolve($reflectionProperty))
        ->toBeTrue();

    $eloquentProperty = EloquentResolver::resolve($reflectionProperty);
    expect($eloquentProperty->getName())
        ->toBe('user')
        ->and($eloquentProperty->isNullable())
        ->toBeTrue()
        ->and($eloquentProperty->isBoolean())
        ->toBeFalse()
        ->and($eloquentProperty->isTraversable())
        ->toBeFalse()
        ->and($eloquentProperty->getParent())
        ->toBeNull()
        ->and($eloquentProperty->getProperties())
        ->toHaveKeys(['id', 'name', 'email', 'locale', 'created_at', 'updated_at'])
        ->not->toHaveKeys(['password', 'remember_token'])
        ->and($eloquentProperty->hasProperties())
        ->toBeTrue()
        ->and($eloquentProperty->getAccessor())
        ->toBe('$user');

    expect($eloquentProperty->getProperties()['name']->getParent())
        ->toBe($eloquentProperty)
        ->and($eloquentProperty->getProperties()['name']->getAccessor())
        ->toBe('$user[\'name\']');
});
