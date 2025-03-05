<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Facades;

use Illuminate\Support\Facades\Facade;

/** @see \MartinPetricko\LaravelDatabaseMail\LaravelDatabaseMail */
class LaravelDatabaseMail extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \MartinPetricko\LaravelDatabaseMail\LaravelDatabaseMail::class;
    }
}
