<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail;

use MartinPetricko\LaravelDatabaseMail\Events\Contracts\TriggersDatabaseMail;
use MartinPetricko\LaravelDatabaseMail\Mail\EventMail;
use MartinPetricko\LaravelDatabaseMail\Models\MailTemplate;
use MartinPetricko\LaravelDatabaseMail\Properties\Property;
use MartinPetricko\LaravelDatabaseMail\Properties\Resolvers\ResolverInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class LaravelDatabaseMail
{
    /** @return class-string<MailTemplate> */
    public function getMailTemplateModel(): string
    {
        /** @var class-string<MailTemplate> $mailTemplateModel */
        $mailTemplateModel = config('database-mail.models.mail_template');
        return $mailTemplateModel;
    }

    /** @return class-string<EventMail> */
    public function getEventMail(): string
    {
        /** @var class-string<EventMail> $eventMail */
        $eventMail = config('database-mail.event_mail');
        return $eventMail;
    }

    /** @return class-string<TriggersDatabaseMail>[] */
    public function getEvents(): array
    {
        /** @var class-string<TriggersDatabaseMail>[] $events */
        $events = config('database-mail.events');
        return $events;
    }

    /** @return class-string<ResolverInterface>[] */
    public function getResolvers(): array
    {
        /** @var class-string<ResolverInterface>[] $resolvers */
        $resolvers = config('database-mail.resolvers');
        return $resolvers;
    }

    /**
     * @param class-string<TriggersDatabaseMail> $event
     * @return array<Property>
     *
     * @throws ReflectionException
     */
    public function getEventAttributes(string $event): array
    {
        $properties = [];
        foreach ((new ReflectionClass($event))->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $properties[$property->getName()] = $this->resolveProperty($property);
        }

        return array_merge(array_filter($properties), $event::mergeProperties());
    }

    public function resolveProperty(ReflectionProperty $property): ?Property
    {
        foreach ($this->getResolvers() as $resolver) {
            if ($resolver::canResolve($property)) {
                return $resolver::resolve($property);
            }
        }
        return null;
    }
}
