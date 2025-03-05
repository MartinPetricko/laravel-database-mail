<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Properties\Resolvers;

use MartinPetricko\LaravelDatabaseMail\Properties\Property;
use ReflectionNamedType;
use ReflectionProperty;
use RuntimeException;

class StringResolver implements ResolverInterface
{
    public static function canResolve(ReflectionProperty $reflectionProperty): bool
    {
        $reflectionPropertyType = $reflectionProperty->getType();
        if (!$reflectionPropertyType instanceof ReflectionNamedType) {
            return false;
        }

        return in_array($reflectionPropertyType->getName(), ['int', 'float', 'string']);
    }

    public static function resolve(ReflectionProperty $reflectionProperty): Property
    {
        $reflectionPropertyType = $reflectionProperty->getType();
        if (!$reflectionPropertyType instanceof ReflectionNamedType) {
            throw new RuntimeException('Invalid reflection property type');
        }

        return (new Property($reflectionProperty->getName()))
            ->nullable($reflectionPropertyType->allowsNull());
    }
}
