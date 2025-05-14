<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Properties\Resolvers;

use MartinPetricko\LaravelDatabaseMail\Properties\Property;
use phpDocumentor\Reflection\Type;
use ReflectionProperty;

interface SubtypeResolverInterface
{
    public static function canResolveSubtype(ReflectionProperty $reflectionProperty, Type $type): bool;

    /**
     * @return array<Property>
     */
    public static function resolveSubtype(ReflectionProperty $reflectionProperty, Type $type): array;
}
