<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Listeners;

use Illuminate\Mail\Events\MessageSent;
use MartinPetricko\LaravelDatabaseMail\Events\Contracts\TriggersDatabaseMail;

class RunEventOnSuccess
{
    public function handle(MessageSent $event): void
    {
        $triggeringEvent = $event->data['event'] ?? null;
        if (!$triggeringEvent instanceof TriggersDatabaseMail) {
            return;
        }

        $triggeringEvent->onSuccess($event);
    }
}
