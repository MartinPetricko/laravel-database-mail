<?php

declare(strict_types=1);

use MartinPetricko\LaravelDatabaseMail\Example\Events\Registered;
use MartinPetricko\LaravelDatabaseMail\Example\Models\User;
use MartinPetricko\LaravelDatabaseMail\Recipients\Recipient;

beforeEach(function () {
    $this->user = User::make([
        'name' => 'John Doe',
        'email' => 'john@doe.com',
        'password' => Hash::make('password'),
    ]);
});

it('can return recipient name', function () {
    $recipient = new Recipient('Registered User', fn () => $this->user);
    expect($recipient->getName())
        ->toBe('Registered User');
});

it('can resolve recipient', function () {
    $recipient = new Recipient('Registered User', fn (Registered $event) => $event->user);

    expect($recipient->getRecipient(new Registered($this->user, [User::make()])))
        ->toBeArray()
        ->toHaveLength(1)
        ->toContainEqual($this->user);
});

it('can resolve recipients from array', function () {
    $secondUser = User::make();

    $recipient = new Recipient('Registered User', fn (Registered $event) => [
        $event->user,
        $secondUser,
    ]);

    expect($recipient->getRecipient(new Registered($this->user, [User::make()])))
        ->toBeArray()
        ->toHaveLength(2)
        ->toContainEqual($this->user)
        ->toContainEqual($secondUser);
});
