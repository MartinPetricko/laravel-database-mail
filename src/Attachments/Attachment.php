<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Attachments;

use Closure;
use Illuminate\Mail\Attachment as LaravelAttachment;
use Illuminate\Support\Facades\App;
use MartinPetricko\LaravelDatabaseMail\Events\Contracts\TriggersDatabaseMail;

/**
 * @template TEvent of TriggersDatabaseMail
 */
class Attachment
{
    /**
     * Name of the attachment that can be shown in the UI.
     *
     * @var string
     */
    protected string $name;

    /**
     * Closure that recieves event as param and returns Mailable Attachment.
     *
     * @var Closure(TEvent $event): (LaravelAttachment|LaravelAttachment[])
     */
    protected Closure $attachment;

    /**
     * @param Closure(TEvent $event): (LaravelAttachment|LaravelAttachment[]) $attachment
     */
    public function __construct(string $name, Closure $attachment)
    {
        $this->name = $name;
        $this->attachment = $attachment;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return LaravelAttachment[]
     */
    public function getAttachment(TriggersDatabaseMail $event): array
    {
        /** @var LaravelAttachment|LaravelAttachment[] $attachments */
        $attachments = App::call($this->attachment, ['event' => $event]);
        if (is_array($attachments)) {
            return $attachments;
        }
        return [$attachments];
    }
}
