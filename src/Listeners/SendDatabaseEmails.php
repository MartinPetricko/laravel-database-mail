<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Listeners;

use Carbon\CarbonInterval;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use MartinPetricko\LaravelDatabaseMail\Events\Contracts\TriggersDatabaseMail;
use MartinPetricko\LaravelDatabaseMail\Exceptions\DatabaseMailException;
use MartinPetricko\LaravelDatabaseMail\Facades\LaravelDatabaseMail;
use MartinPetricko\LaravelDatabaseMail\Mail\EventMail;
use MartinPetricko\LaravelDatabaseMail\Models\MailTemplate;
use Throwable;

class SendDatabaseEmails
{
    public function handle(TriggersDatabaseMail $event): void
    {
        $mailTemplates = LaravelDatabaseMail::getMailTemplateModel()::where('event', $event::class)->where('is_active', true)->get();

        foreach ($mailTemplates as $mailTemplate) {
            try {
                $delayInterval = $this->getDelayInterval($mailTemplate);
                foreach ($this->getRecipients($event, $mailTemplate) as $recipient) {
                    /** @var EventMail $mailable */
                    $mailable = App::make(LaravelDatabaseMail::getEventMail(), ['mailTemplate' => $mailTemplate, 'event' => $event]);
                    Mail::to($recipient)->later($delayInterval, $mailable);
                }
            } catch (Throwable $e) {
                throw new DatabaseMailException($mailTemplate, $event, $e->getMessage(), $e->getCode(), $e);
            }
        }
    }

    /** @return array<mixed> */
    protected function getRecipients(TriggersDatabaseMail $event, MailTemplate $mailTemplate): array
    {
        $recipients = [];
        foreach (array_intersect_key($event::getRecipients(), array_flip($mailTemplate->recipients)) as $recipient) {
            $recipients[] = $recipient->getRecipient($event);
        }
        return array_merge(...$recipients);
    }

    protected function getDelayInterval(MailTemplate $mailTemplate): CarbonInterval
    {
        try {
            return CarbonInterval::createFromDateString($mailTemplate->delay ?: '');
        } catch (InvalidFormatException) {
            return CarbonInterval::seconds(0);
        }
    }
}
