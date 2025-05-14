<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Properties\Resolvers;

use MartinPetricko\LaravelDatabaseMail\Properties\Property;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\String_;
use ReflectionNamedType;
use ReflectionProperty;
use RuntimeException;

class StringResolver implements ResolverInterface, SubtypeResolverInterface
{
    public static function canResolve(ReflectionProperty $reflectionProperty): bool
    {
        $reflectionPropertyType = $reflectionProperty->getType();
        if (!$reflectionPropertyType instanceof ReflectionNamedType) {
            return false;
        }

        return in_array($reflectionPropertyType->getName(), ['int', 'float', 'string']);
    }

    public static function canResolveSubtype(ReflectionProperty $reflectionProperty, Type $type): bool
    {
        return $type instanceof String_
            || $type instanceof Integer
            || $type instanceof Float_;
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

    public static function resolveSubtype(ReflectionProperty $reflectionProperty, Type $type): array
    {
        return [];
    }
}
