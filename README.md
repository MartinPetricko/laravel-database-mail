# Laravel Database Mail

[![Latest Version on Packagist](https://img.shields.io/packagist/v/martinpetricko/laravel-database-mail.svg?style=flat-square)](https://packagist.org/packages/martinpetricko/laravel-database-mail)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/martinpetricko/laravel-database-mail/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/martinpetricko/laravel-database-mail/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/martinpetricko/laravel-database-mail/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/martinpetricko/laravel-database-mail/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/martinpetricko/laravel-database-mail.svg?style=flat-square)](https://packagist.org/packages/martinpetricko/laravel-database-mail)

This package allows you to store email templates in your database, assign them to events and send them when the event is
fired. For implementation of this package check
out [FilamentPHP implementation](https://github.com/MartinPetricko/filament-database-mail-docs).

## Support me

[//]: # (TODO: add link to filament package)
You can support me by [buying FilamentPHP implementation of this package](https://filamentphp.com/plugins).

## Installation

You can install the package via composer:

```bash
composer require martinpetricko/laravel-database-mail
```

Publish and run the migrations with:

```bash
php artisan vendor:publish --tag="database-mail-migrations"
php artisan migrate
```

Publish the config file with:

```bash
php artisan vendor:publish --tag="database-mail-config"
```

This is the contents of the published config file:

```php
return [
    /**
     * Register event listener for all TriggersDatabaseMail events,
     * that sends mails associated with the event.
     */
    'register_event_listener' => true,

    /**
     * Period of time when mail exceptions are pruned.
     */
    'prune_exceptions_period' => now()->subMonth(),

    /**
     * Models that are used by Laravel Database Mail.
     */
    'models' => [
        'mail_exception' => \MartinPetricko\LaravelDatabaseMail\Models\MailException::class,
        'mail_template' => \MartinPetricko\LaravelDatabaseMail\Models\MailTemplate::class,
    ],

    /**
     * Mailable that is used to send the mail from database.
     */
    'event_mail' => \MartinPetricko\LaravelDatabaseMail\Mail\EventMail::class,

    /**
     * Resolvers are used to automatically resolve properties of the event.
     * This property definitions can be later shown to user as available
     * variables that can be used in the mail template.
     */
    'resolvers' => [
        \MartinPetricko\LaravelDatabaseMail\Properties\Resolvers\EloquentResolver::class,
        \MartinPetricko\LaravelDatabaseMail\Properties\Resolvers\BooleanResolver::class,
        \MartinPetricko\LaravelDatabaseMail\Properties\Resolvers\StringResolver::class,
    ],

    /**
     * Register events that implement TriggersDatabaseMail interface.
     * Events will be used to trigger the mail and this list
     * of events can be shown to user as available events.
     */
    'events' => [
        // \Illuminate\Auth\Events\Registered::class,
    ],
];
```

Register excepitons reporting in `bootstrap/app.php`:

```php
use Illuminate\Foundation\Configuration\Exceptions;
use MartinPetricko\LaravelDatabaseMail\Exceptions\DatabaseMailException;
use MartinPetricko\LaravelDatabaseMail\Facades\LaravelDatabaseMail;

//...

->withExceptions(function (Exceptions $exceptions) {
    $exceptions->report(function (DatabaseMailException $e) {
        LaravelDatabaseMail::logException($e);
    });
})
```

Enable exceptions table pruning:

```php
Schedule::command('model:prune', [
    '--model' => [
        MartinPetricko\LaravelDatabaseMail\Models\MailException::class,
    ],
])->daily();
```

## Usage

### Create Events

Add `TriggersDatabaseMail` interface and `CanTriggerDatabaseMail` trait to your standard laravel events.

```php
namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MartinPetricko\LaravelDatabaseMail\Attachments\Attachment;
use MartinPetricko\LaravelDatabaseMail\Events\Concerns\CanTriggerDatabaseMail;
use MartinPetricko\LaravelDatabaseMail\Events\Contracts\TriggersDatabaseMail;
use MartinPetricko\LaravelDatabaseMail\Recipients\Recipient;

class Registered implements TriggersDatabaseMail
{
    use Dispatchable;
    use SerializesModels;
    use CanTriggerDatabaseMail;
    
    /**
     * All public properties of the event will be passed 
     * to the mail template body and subject.
     */
    public function __construct(public User $user, public string $emailVerificationUrl)
    {
        //
    }

    /**
     * Name of the event that can be used in the UI.
     */
    public static function getName(): string
    {
        return 'User Registered';
    }

    /**
     * Description of the event that can be used in the UI.
     */
    public static function getDescription(): ?string
    {
        return 'Fires when a user is registered';
    }

    /**
     * List of possible recipients that can receive the mail.
     * MailTemplate stores keys of recipients that will
     * receive the mail when event is fired.
     *
     * @return array<string, Recipient>
     */
    public static function getRecipients(): array
    {
        return [
            'user' => new Recipient('Registered User', fn (Registered $event) => $event->user),
        ];
    }

    /**
     * List of possible attachments that can be attached to the mail.
     * MailTemplate stores keys of attachments that will be
     * attached to the mail when event is fired.
     *
     * @return array<string, Attachment>
     */
    public static function getAttachments(): array
    {
        return [
            'terms-of-service' => new Attachment('Terms of Services', fn (Registered $event) => [
                \Illuminate\Mail\Mailables\Attachment::fromUrl('https://my-project.com/tos')->as('tos.pdf')
            ]),
        ];
    }
}
```

### Register Events

Add list of events to your published `config/database-mail.php` file:

```php
'events' => [
    \App\Events\Registered::class,
],
```

### Create Mail Template

```php
use \MartinPetricko\LaravelDatabaseMail\Models\MailTemplate;

$mailTemplate = MailTemplate::make();

// Internal name of the mail
$mailTemplate->name = 'Verify Email Address';

// The event that triggers the mail
$mailTemplate->event = \App\Events\Registered::class;

// The subject of the mail rendered with blade
$mailTemplate->subject = 'Welcome {{ $user->name }}';

// The body of the mail rendered with blade
$mailTemplate->body = <<<HTML
    <h1>Welcome {{ \$user->name }}</h1>
    <p>Please verify your email address by clicking <a href="{{ \$emailVerificationUrl }}">Here</a></p>
HTML;

// Keys of the recipients from event defined recipients that will receive the mail
$mailTemplate->recipients = ['user'];

// Keys of the attachments from event defined attachments that will be attache to the mail
$mailTemplate->attachments = ['terms-of-service'];

// Optionaly you can set delay for how long should mail be sent after event was fired
$mailTemplate->delay = '1 day 5 hours';

// Determines if mail is sent when event is fired
$mailTemplate->is_active = true;

$mailTemplate->save();
```

### Dispatch Event

Dispatch event as you would any other laravel event with it's parameters.

```php
use App\Events\Registered;

// ... your bussiness logic

Registered::dispatch($registeredUser, $registeredUserEmailVerificationUrl);
```

### List Events

You can get all events that are registered in `config/database-mail.php`.

```php
use MartinPetricko\LaravelDatabaseMail\Facades\LaravelDatabaseMail;

LaravelDatabaseMail::getEvents();
```

You can get array of property definitions that can be shown to user as available variables that can be used in mail
template.

```php
use MartinPetricko\LaravelDatabaseMail\Facades\LaravelDatabaseMail;

LaravelDatabaseMail::getEventAttributes(\App\Events\Registered::class);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Martin Petricko](https://github.com/MartinPetricko)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
