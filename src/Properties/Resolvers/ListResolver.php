<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Properties\Resolvers;

use MartinPetricko\LaravelDatabaseMail\Facades\LaravelDatabaseMail;
use MartinPetricko\LaravelDatabaseMail\Properties\Property;
use MartinPetricko\LaravelDatabaseMail\Utils\ReflectionType;
use phpDocumentor\Reflection\Types\AbstractList;
use phpDocumentor\Reflection\Types\Nullable;
use ReflectionNamedType;
use ReflectionProperty;
use RuntimeException;

class ListResolver implements ResolverInterface
{
    public static function canResolve(ReflectionProperty $reflectionProperty): bool
    {
        $reflectionPropertyType = $reflectionProperty->getType();
        if (!$reflectionPropertyType instanceof ReflectionNamedType) {
            return false;
        }

        return ReflectionType::getPropertyType($reflectionProperty) instanceof AbstractList;
    }

    public static function resolve(ReflectionProperty $reflectionProperty): Property
    {
        $reflectionPropertyType = $reflectionProperty->getType();
        if (!$reflectionPropertyType instanceof ReflectionNamedType) {
            throw new RuntimeException('Invalid reflection property type');
        }

        $type = ReflectionType::getPropertyType($reflectionProperty);
        if (!$type instanceof AbstractList) {
            throw new RuntimeException('Invalid reflection property type');
        }

        $property = (new Property($reflectionProperty->getName()))
            ->nullable($reflectionPropertyType->allowsNull())
            ->traversable();

        $properties = static::getSubProperties($reflectionProperty, $type);

        if ($properties === null) {
            $property->hidden();
        } else {
            $property->properties($properties);
        }

        return $property;
    }

    /**
     * @return ?array<Property>
     */
    protected static function getSubProperties(ReflectionProperty $reflectionProperty, AbstractList $type): ?array
    {
        $valueType = $type->getValueType();
        if ($valueType instanceof Nullable) {
            $valueType = $valueType->getActualType();
        }

        return LaravelDatabaseMail::resolveSubProperty($reflectionProperty, $valueType);
    }
}
