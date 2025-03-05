<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Properties\Resolvers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use MartinPetricko\LaravelDatabaseMail\Properties\Property;
use ReflectionNamedType;
use ReflectionProperty;
use RuntimeException;

class EloquentResolver implements ResolverInterface
{
    public static function canResolve(ReflectionProperty $reflectionProperty): bool
    {
        $reflectionPropertyType = $reflectionProperty->getType();
        if (!$reflectionPropertyType instanceof ReflectionNamedType) {
            return false;
        }

        return is_a($reflectionPropertyType->getName(), Model::class, true);
    }

    public static function resolve(ReflectionProperty $reflectionProperty): Property
    {
        $reflectionPropertyType = $reflectionProperty->getType();
        if (!$reflectionPropertyType instanceof ReflectionNamedType) {
            throw new RuntimeException('Invalid reflection property type');
        }

        /** @var Model $model */
        $model = new ($reflectionPropertyType->getName());
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

        return (new Property($reflectionProperty->getName()))
            ->nullable($reflectionPropertyType->allowsNull())
            ->properties($modelProperties);
    }
}
