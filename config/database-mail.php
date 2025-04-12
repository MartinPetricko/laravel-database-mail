<?php

declare(strict_types=1);

/**
 * Config for MartinPetricko/LaravelDatabaseMail
 */
return [
    /**
     * Register event listener for all TriggersDatabaseMail events,
     * that sends mails associated with the event.
     */
    'register_event_listener' => true,

    /**
     * Period of time when mail exceptions are pruned.
     */
    'prune_exceptions_period' => now()->subMonth(),

    /**
     * Models that are used by Laravel Database Mail.
     */
    'models' => [
        'mail_exception' => \MartinPetricko\LaravelDatabaseMail\Models\MailException::class,
        'mail_template' => \MartinPetricko\LaravelDatabaseMail\Models\MailTemplate::class,
    ],

    /**
     * Mailable that is used to send the mail from database.
     */
    'event_mail' => \MartinPetricko\LaravelDatabaseMail\Mail\EventMail::class,

    /**
     * Resolvers are used to automatically resolve properties of the event.
     * These property definitions can be later shown to user as available
     * variables that can be used in the mail template.
     */
    'resolvers' => [
        \MartinPetricko\LaravelDatabaseMail\Properties\Resolvers\EloquentResolver::class,
        \MartinPetricko\LaravelDatabaseMail\Properties\Resolvers\BooleanResolver::class,
        \MartinPetricko\LaravelDatabaseMail\Properties\Resolvers\StringResolver::class,
    ],

    /**
     * Register events that implement TriggersDatabaseMail interface.
     * Events will be used to trigger the mail and this list
     * of events can be shown to user as available events.
     */
    'events' => [
        // \App\Events\YourEvent::class
    ],
];
