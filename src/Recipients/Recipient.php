<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Recipients;

use Closure;
use Illuminate\Support\Facades\App;
use MartinPetricko\LaravelDatabaseMail\Events\Contracts\TriggersDatabaseMail;

class Recipient
{
    /**
     * Name of the recipient that can be shown in the UI.
     *
     * @var string
     */
    protected string $name;

    /**
     * Closure that recieves event as param and returns mail recipient
     * that will be passed to Mail::to() method..
     *
     * @var Closure(mixed $event): mixed
     */
    protected Closure $recipient;

    /**
     * @param Closure(mixed $event): mixed $recipient
     */
    public function __construct(string $name, Closure $recipient)
    {
        $this->name = $name;
        $this->recipient = $recipient;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRecipient(TriggersDatabaseMail $event): mixed
    {
        return App::call($this->recipient, ['event' => $event]);
    }
}
