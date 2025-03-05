<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Example\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Mail\Mailables\Attachment as MailAttachment;
use Illuminate\Queue\SerializesModels;
use MartinPetricko\LaravelDatabaseMail\Attachments\Attachment;
use MartinPetricko\LaravelDatabaseMail\Events\Concerns\CanTriggerDatabaseMail;
use MartinPetricko\LaravelDatabaseMail\Events\Contracts\TriggersDatabaseMail;
use MartinPetricko\LaravelDatabaseMail\Example\Models\User;
use MartinPetricko\LaravelDatabaseMail\Properties\Property;
use MartinPetricko\LaravelDatabaseMail\Recipients\Recipient;

class Registered implements TriggersDatabaseMail
{
    use Dispatchable;
    use SerializesModels;
    use CanTriggerDatabaseMail;

    /**
     * @param array<string, string>|null $additionalData
     */
    public function __construct(public User $user, public ?array $additionalData = null)
    {
        //
    }

    public static function getName(): string
    {
        return 'User Registered';
    }

    public static function getDescription(): string
    {
        return 'Fires when a user is registered';
    }

    /**
     * @return Recipient<Registered>[]
     */
    public static function getRecipients(): array
    {
        return [
            'registered-user' => new Recipient('Registered User', fn (Registered $event) => $event->user),
        ];
    }

    /**
     * @return Attachment<Registered>[]
     */
    public static function getAttachments(): array
    {
        return [
            'terms-of-service' => new Attachment('Terms of Service', fn (Registered $event) => [
                MailAttachment::fromUrl('https://your-app.com/tos')->as('tos.pdf'),
            ]),
        ];
    }

    public static function mergeProperties(): array
    {
        return [
            'additionalData' => (new Property('additionalData'))
                ->nullable()
                ->properties([
                    new Property('lorem'),
                    (new Property('ipsum'))
                        ->nullable(),
                ]),
        ];
    }
}
