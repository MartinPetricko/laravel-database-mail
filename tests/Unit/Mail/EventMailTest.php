<?php

declare(strict_types=1);

use MartinPetricko\LaravelDatabaseMail\Example\Events\Registered;
use MartinPetricko\LaravelDatabaseMail\Example\Models\User;
use MartinPetricko\LaravelDatabaseMail\Mail\EventMail;
use MartinPetricko\LaravelDatabaseMail\Models\MailTemplate;

beforeEach(function () {
    $mailTemplate = MailTemplate::make([
        'name' => 'User Wellcome Email',
        'event' => Registered::class,
        'subject' => 'Welcome',
        'body' => '<h1>Welcome</h1>',
        'recipients' => ['registered-user'],
        'attachments' => [],
        'delay' => null,
        'is_active' => true,
    ]);

    $user = User::make([
        'name' => 'John Doe',
        'email' => 'john@doe.com',
        'password' => Hash::make('password'),
    ]);

    $this->mail = new EventMail($mailTemplate, new Registered($user, []));
});

it('keeps distinct addresses of multi address recipients', function () {
    $this->mail->to(new class () {
        /** @var array<string> */
        public array $email = ['john@doe.com', 'jane@doe.com'];
    });

    expect($this->mail->to)
        ->toBe([
            ['name' => null, 'address' => 'john@doe.com'],
            ['name' => null, 'address' => 'jane@doe.com'],
        ]);
});

it('removes duplicate addresses of multi address recipients', function () {
    $this->mail->to(new class () {
        /** @var array<string> */
        public array $email = ['john@doe.com', 'john@doe.com', 'jane@doe.com'];
    });

    expect($this->mail->to)
        ->toBe([
            ['name' => null, 'address' => 'john@doe.com'],
            ['name' => null, 'address' => 'jane@doe.com'],
        ]);
});

it('removes case insensitive duplicate addresses', function () {
    $this->mail->to(new class () {
        /** @var array<string> */
        public array $email = ['john@doe.com', 'JOHN@doe.com'];
    });

    expect($this->mail->to)
        ->toBe([
            ['name' => null, 'address' => 'john@doe.com'],
        ]);
});

it('removes duplicate addresses across separate to calls', function () {
    $this->mail->to('john@doe.com', 'John Doe');
    $this->mail->to('JOHN@doe.com');

    expect($this->mail->to)
        ->toBe([
            ['name' => 'John Doe', 'address' => 'john@doe.com'],
        ]);
});

it('removes duplicate addresses of cc and bcc recipients', function () {
    $this->mail->cc(['john@doe.com', 'john@doe.com']);
    $this->mail->bcc(['jane@doe.com', 'JANE@doe.com']);

    expect($this->mail->cc)
        ->toBe([
            ['name' => null, 'address' => 'john@doe.com'],
        ])
        ->and($this->mail->bcc)
        ->toBe([
            ['name' => null, 'address' => 'jane@doe.com'],
        ]);
});
