<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Utils;

use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Type;
use ReflectionProperty;

class ReflectionType
{
    public static function getPropertyType(ReflectionProperty $reflectionProperty): ?Type
    {
        if ($reflectionProperty->isPromoted()) {
            $docBlock = $reflectionProperty->getDeclaringClass()->getConstructor()?->getDocComment() ?: false;
        } else {
            $docBlock = $reflectionProperty->getDocComment();
        }

        if ($docBlock === false) {
            return null;
        }

        $docBlock = DocBlockFactory::createInstance()->create($docBlock);

        if ($reflectionProperty->isPromoted()) {
            /** @var Param $tag */
            foreach ($docBlock->getTagsWithTypeByName('param') as $tag) {
                if ($tag->getVariableName() !== $reflectionProperty->getName()) {
                    continue;
                }
                return $tag->getType();
            }
        } else {
            /** @var Var_ $tag */
            foreach ($docBlock->getTagsWithTypeByName('var') as $tag) {
                if (empty($tag->getVariableName())) {
                    $type = $tag->getType();
                    break;
                }

                if ($tag->getVariableName() !== $reflectionProperty->getName()) {
                    continue;
                }

                return $tag->getType();
            }
        }

        return null;
    }
}
