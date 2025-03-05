<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Events\Contracts;

use MartinPetricko\LaravelDatabaseMail\Attachments\Attachment;
use MartinPetricko\LaravelDatabaseMail\Properties\Property;
use MartinPetricko\LaravelDatabaseMail\Recipients\Recipient;

interface TriggersDatabaseMail
{
    /**
     * Name of the event that can be used in the UI.
     */
    public static function getName(): string;

    /**
     * Description of the event that can be used in the UI.
     */
    public static function getDescription(): ?string;

    /**
     * List of possible recipients that can receive the mail.
     * MailTemplate stores keys of recipients that will
     * receive the mail when event is fired.
     *
     * @return array<string, Recipient>
     */
    public static function getRecipients(): array;

    /**
     * List of possible attachments that can be attached to the mail.
     * MailTemplate stores keys of attachments that will be
     * attached to the mail when event is fired.
     *
     * @return array<string, Attachment>
     */
    public static function getAttachments(): array;

    /**
     * List of additional properties that can be used in the mail template
     * and were not automatically resolved via registered resolvers.
     *
     * @return array<string, Property>
     */
    public static function mergeProperties(): array;

    /**
     * List of attributes that will be passed the mail template.
     *
     * @return array<string, mixed>
     */
    public function getAttributes(): array;
}
