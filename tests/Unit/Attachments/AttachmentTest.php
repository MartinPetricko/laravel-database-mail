<?php

declare(strict_types=1);

use Illuminate\Mail\Mailables\Attachment as MailAttachment;
use MartinPetricko\LaravelDatabaseMail\Attachments\Attachment;
use MartinPetricko\LaravelDatabaseMail\Example\Events\Registered;
use MartinPetricko\LaravelDatabaseMail\Example\Models\User;

it('can return attachment name', function () {
    $attachment = new Attachment('Test Attachment', fn () => MailAttachment::fromUrl('https://your-app.com/tos'));
    expect($attachment->getName())
        ->toBe('Test Attachment');
});

it('can resolve attachment', function () {
    $mailAttachment = MailAttachment::fromUrl('https://your-app.com/tos')->as('tos.pdf');

    $attachment = new Attachment('Test Attachment', fn () => $mailAttachment);

    expect($attachment->getAttachment(new Registered(User::make(), [User::make()])))
        ->toBeArray()
        ->toHaveLength(1)
        ->toContainEqual($mailAttachment);
});

it('can resolve attachments from array', function () {
    $firstMailAttachment = MailAttachment::fromUrl('https://your-app.com/tos')->as('tos.pdf');
    $secondMailAttachment = MailAttachment::fromUrl('https://your-app.com/gdpr')->as('gdpr.pdf');

    $attachment = new Attachment('Test Attachment', fn () => [
        $firstMailAttachment,
        $secondMailAttachment,
    ]);

    expect($attachment->getAttachment(new Registered(User::make(), [User::make()])))
        ->toBeArray()
        ->toHaveLength(2)
        ->toContainEqual($firstMailAttachment)
        ->toContainEqual($secondMailAttachment);
});
