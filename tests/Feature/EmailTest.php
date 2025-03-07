<?php

declare(strict_types=1);

use Illuminate\Mail\Mailables\Attachment;
use MartinPetricko\LaravelDatabaseMail\Example\Events\Registered;
use MartinPetricko\LaravelDatabaseMail\Example\Models\User;
use MartinPetricko\LaravelDatabaseMail\Exceptions\DatabaseMailException;
use MartinPetricko\LaravelDatabaseMail\Facades\LaravelDatabaseMail;
use MartinPetricko\LaravelDatabaseMail\Mail\EventMail;
use MartinPetricko\LaravelDatabaseMail\Models\MailException;
use MartinPetricko\LaravelDatabaseMail\Models\MailTemplate;

beforeEach(function () {
    Mail::fake();

    $this->user = User::create([
        'name' => 'John Doe',
        'email' => 'john@doe.com',
        'password' => Hash::make('password'),
    ]);
});

it('can send mail after event was fired', function () {
    MailTemplate::create([
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

it('can create mail exception', function () {
    MailTemplate::create([
        'name' => 'User Wellcome Email',
        'event' => Registered::class,
        'subject' => 'Welcome {{ $user->name }}',
        'body' => <<<HTML
            <h1>Hi {{ \$user->name }}</h1>
            @if(\$additionalData !== null)
                <p>Lorem: {{ \$additionalData[invalidSyntax }}</p>
            @endif
        HTML,
        'recipients' => ['registered-user'],
        'attachments' => ['terms-of-service'],
        'delay' => null,
        'is_active' => true,
    ]);

    Registered::dispatch($this->user);


    Mail::assertQueued(EventMail::class, function (EventMail $mail) {
        try {
            $mail->assertSeeInHtml('Hi John Doe');
        } catch (DatabaseMailException $e) {
            LaravelDatabaseMail::logException($e);
        }

        return true;
    });

    expect(MailException::count())
        ->toBe(1);
});
