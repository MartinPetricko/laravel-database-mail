<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Blade;
use MartinPetricko\LaravelDatabaseMail\Events\Contracts\TriggersDatabaseMail;
use MartinPetricko\LaravelDatabaseMail\Exceptions\DatabaseMailException;
use MartinPetricko\LaravelDatabaseMail\Models\MailTemplate;
use Throwable;

class EventMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(public MailTemplate $mailTemplate, public TriggersDatabaseMail $event)
    {
        //
    }

    public function envelope(): Envelope
    {
        try {
            return new Envelope(
                subject: Blade::render($this->mailTemplate->subject, $this->event->getAttributes()),
            );
        } catch (Throwable $e) {
            throw new DatabaseMailException($this->mailTemplate, $this->event, $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function content(): Content
    {
        try {
            return new Content(
                htmlString: Blade::render($this->mailTemplate->body, $this->event->getAttributes()),
            );
        } catch (Throwable $e) {
            throw new DatabaseMailException($this->mailTemplate, $this->event, $e->getMessage(), $e->getCode(), $e);
        }
    }

    /** @return array<Attachment> */
    public function attachments(): array
    {
        $attachments = [];
        foreach (array_intersect_key($this->event::getAttachments(), array_flip($this->mailTemplate->attachments)) as $attachment) {
            $attachments[] = $attachment->getAttachment($this->event);
        }
        return array_merge(...$attachments);
    }
}
