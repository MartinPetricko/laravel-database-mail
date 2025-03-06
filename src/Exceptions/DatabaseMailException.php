<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Exceptions;

use Exception;
use MartinPetricko\LaravelDatabaseMail\Events\Contracts\TriggersDatabaseMail;
use MartinPetricko\LaravelDatabaseMail\Models\MailTemplate;
use Throwable;

class DatabaseMailException extends Exception
{
    private MailTemplate $mailTemplate;

    private TriggersDatabaseMail $event;

    public function __construct(MailTemplate $mailTemplate, TriggersDatabaseMail $event, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->mailTemplate = $mailTemplate;
        $this->event = $event;
    }

    public function getMailTemplate(): MailTemplate
    {
        return $this->mailTemplate;
    }

    public function getEvent(): TriggersDatabaseMail
    {
        return $this->event;
    }
}
