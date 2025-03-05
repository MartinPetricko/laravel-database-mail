<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Events\Concerns;

use MartinPetricko\LaravelDatabaseMail\Attachments\Attachment;
use MartinPetricko\LaravelDatabaseMail\Events\Contracts\TriggersDatabaseMail;
use MartinPetricko\LaravelDatabaseMail\Facades\LaravelDatabaseMail;
use MartinPetricko\LaravelDatabaseMail\Properties\Property;

/**
 * @phpstan-require-implements TriggersDatabaseMail
 */
trait CanTriggerDatabaseMail
{
    public static function getDescription(): ?string
    {
        return null;
    }

    /** @return array<string, Attachment> */
    public static function getAttachments(): array
    {
        return [];
    }

    /** @return array<string, Property> */
    public static function mergeProperties(): array
    {
        return [];
    }

    public function getAttributes(): array
    {
        $attributes = [];
        foreach (LaravelDatabaseMail::getEventAttributes($this::class) as $attribute) {
            $attributes[$attribute->getName()] = $this->{$attribute->getName()};
        }
        return $attributes;
    }
}
