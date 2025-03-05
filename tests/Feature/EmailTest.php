<?php

declare(strict_types=1);

use Illuminate\Mail\Mailables\Attachment;
use MartinPetricko\LaravelDatabaseMail\Example\Events\Registered;
use MartinPetricko\LaravelDatabaseMail\Example\Models\User;
use MartinPetricko\LaravelDatabaseMail\Mail\EventMail;
use MartinPetricko\LaravelDatabaseMail\Models\MailTemplate;

beforeEach(function () {
    $this->user = User::create([
        'name' => 'John Doe',
        'email' => 'john@doe.com',
        'password' => Hash::make('password'),
    ]);

    $this->mailTemplate = MailTemplate::create([
        'name' => 'User Wellcome Email',
        'event' => Registered::class,
        'subject' => 'Welcome {{ $user->name }}',
        'body' => <<<HTML
            <h1>Hi {{ \$user->name }}</h1>
            @if(\$additionalData !== null)
                <p>Lorem: {{ \$additionalData['lorem'] }}</p>
            @endif
        HTML,
        'recipients' => ['registered-user'],
        'attachments' => ['terms-of-service'],
        'delay' => null,
        'is_active' => true,
    ]);
});

it('can send mail after event was fired', function () {
    Mail::fake();

    Registered::dispatch($this->user);

    Mail::assertQueued(EventMail::class, function (EventMail $mail) {
        return $mail
            ->assertHasTo('john@doe.com')
            ->assertHasSubject('Welcome John Doe')
            ->assertSeeInHtml('Hi John Doe')
            ->assertDontSeeInHtml('Lorem')
            ->assertHasAttachment(Attachment::fromUrl('https://your-app.com/tos')->as('tos.pdf'));
    });
});
