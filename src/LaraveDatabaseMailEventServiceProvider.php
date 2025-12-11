<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Illuminate\Mail\Events\MessageSent;
use MartinPetricko\LaravelDatabaseMail\Events\Contracts\TriggersDatabaseMail;
use MartinPetricko\LaravelDatabaseMail\Listeners\RunEventOnSuccess;
use MartinPetricko\LaravelDatabaseMail\Listeners\SendDatabaseEmails;

class LaraveDatabaseMailEventServiceProvider extends EventServiceProvider
{
    protected $listen = [
        TriggersDatabaseMail::class => [
            SendDatabaseEmails::class,
        ],
        MessageSent::class => [
            RunEventOnSuccess::class,
        ],
    ];
}
