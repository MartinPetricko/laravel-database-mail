<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Tests\Fixtures\Events;

use Illuminate\Foundation\Events\Dispatchable;
use MartinPetricko\LaravelDatabaseMail\Events\Concerns\CanTriggerDatabaseMail;
use MartinPetricko\LaravelDatabaseMail\Events\Contracts\TriggersDatabaseMail;
use MartinPetricko\LaravelDatabaseMail\Recipients\Recipient;

class NewsletterSent implements TriggersDatabaseMail
{
    use CanTriggerDatabaseMail;
    use Dispatchable;

    /**
     * @param array<string> $subscriberEmails
     */
    public function __construct(public array $subscriberEmails)
    {
        //
    }

    public static function getName(): string
    {
        return 'Newsletter Sent';
    }

    /**
     * @return array<string, Recipient<self>>
     */
    public static function getRecipients(): array
    {
        return [
            'subscribers' => new Recipient('Subscribers', fn (NewsletterSent $event): object => new class ($event->subscriberEmails) {
                /**
                 * @param array<string> $email
                 */
                public function __construct(public array $email)
                {
                    //
                }
            }),
        ];
    }
}
