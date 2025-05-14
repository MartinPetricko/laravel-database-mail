<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail;

use MartinPetricko\LaravelDatabaseMail\Events\Contracts\TriggersDatabaseMail;
use MartinPetricko\LaravelDatabaseMail\Exceptions\DatabaseMailException;
use MartinPetricko\LaravelDatabaseMail\Exceptions\SubtypeResolverNotFound;
use MartinPetricko\LaravelDatabaseMail\Mail\EventMail;
use MartinPetricko\LaravelDatabaseMail\Models\MailException;
use MartinPetricko\LaravelDatabaseMail\Models\MailTemplate;
use MartinPetricko\LaravelDatabaseMail\Properties\Property;
use MartinPetricko\LaravelDatabaseMail\Properties\Resolvers\ResolverInterface;
use MartinPetricko\LaravelDatabaseMail\Properties\Resolvers\SubtypeResolverInterface;
use phpDocumentor\Reflection\Type;
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

    /** @return class-string<MailException> */
    public function getMailExceptionModel(): string
    {
        /** @var class-string<MailException> $mailExceptionModel */
        $mailExceptionModel = config('database-mail.models.mail_exception');
        return $mailExceptionModel;
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

    /** @return class-string[] */
    public function getResolvers(): array
    {
        /** @var class-string[] $resolvers */
        $resolvers = config('database-mail.resolvers');
        return $resolvers;
    }

    public function logException(DatabaseMailException $exception): void
    {
        $exception->getMailTemplate()->exceptions()->create([
            'data' => $exception->getEvent()->getAttributes(),
            'type' => get_class($exception),
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'file' => $exception->getPrevious()?->getFile(),
            'line' => $exception->getPrevious()?->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);
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
            if (is_subclass_of($resolver, ResolverInterface::class) && $resolver::canResolve($property)) {
                return $resolver::resolve($property);
            }
        }
        return (new Property($property->getName()))
            ->hidden();
    }

    /**
     * @return array<Property>
     * @throws SubtypeResolverNotFound
     */
    public function resolveSubProperty(ReflectionProperty $property, Type $type): array
    {
        foreach ($this->getResolvers() as $resolver) {
            if (is_subclass_of($resolver, SubtypeResolverInterface::class) && $resolver::canResolveSubtype($property, $type)) {
                return $resolver::resolveSubtype($property, $type);
            }
        }
        throw new SubtypeResolverNotFound();
    }
}
