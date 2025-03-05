<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Properties\Resolvers;

use MartinPetricko\LaravelDatabaseMail\Properties\Property;
use ReflectionProperty;

interface ResolverInterface
{
    public static function canResolve(ReflectionProperty $reflectionProperty): bool;

    public static function resolve(ReflectionProperty $reflectionProperty): Property;
}
