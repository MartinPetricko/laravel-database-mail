<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Properties\Resolvers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use MartinPetricko\LaravelDatabaseMail\Properties\Property;
use Nette\Utils\Reflection;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Object_;
use ReflectionNamedType;
use ReflectionProperty;
use RuntimeException;

class EloquentResolver implements ResolverInterface, SubtypeResolverInterface
{
    public static function canResolve(ReflectionProperty $reflectionProperty): bool
    {
        $reflectionPropertyType = $reflectionProperty->getType();
        if (!$reflectionPropertyType instanceof ReflectionNamedType) {
            return false;
        }

        return is_a($reflectionPropertyType->getName(), Model::class, true);
    }

    public static function canResolveSubtype(ReflectionProperty $reflectionProperty, Type $type): bool
    {
        if (!$type instanceof Object_) {
            return false;
        }

        $fqsen = $type->getFqsen()?->getName();
        if ($fqsen === null) {
            return false;
        }

        $class = Reflection::expandClassName($fqsen, $reflectionProperty->getDeclaringClass());

        return is_a($class, Model::class, true);
    }

    public static function resolve(ReflectionProperty $reflectionProperty): Property
    {
        $reflectionPropertyType = $reflectionProperty->getType();
        if (!$reflectionPropertyType instanceof ReflectionNamedType) {
            throw new RuntimeException('Invalid reflection property type');
        }

        /** @var Model $model */
        $model = new ($reflectionPropertyType->getName());

        return (new Property($reflectionProperty->getName()))
            ->nullable($reflectionPropertyType->allowsNull())
            ->properties(static::getModelProperties($model));
    }

    /**
     * @param Object_ $type
     * @return array<Property>
     */
    public static function resolveSubtype(ReflectionProperty $reflectionProperty, Type $type): array
    {
        $fqsen = $type->getFqsen()?->getName();
        if ($fqsen === null) {
            throw new RuntimeException('Invalid reflection property type');
        }

        /** @var class-string<Model> $class */
        $class = Reflection::expandClassName($fqsen, $reflectionProperty->getDeclaringClass());

        /** @var Model $model */
        $model = new $class();

        return static::getModelProperties($model);
    }

    /**
     * @return array<Property>
     */
    protected static function getModelProperties(Model $model): array
    {
        $modelProperties = [];

        /** @var array<array{name: string, nullable: bool}> $schema */
        $schema = Schema::getColumns($model->getTable());
        foreach ($schema as $column) {
            if (in_array($column['name'], $model->getHidden(), true)) {
                continue;
            }

            $modelProperties[$column['name']] = (new Property($column['name']))
                ->nullable($column['nullable']);
        }

        return $modelProperties;
    }
}
