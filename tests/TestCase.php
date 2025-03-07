<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Schema;
use MartinPetricko\LaravelDatabaseMail\Exceptions\DatabaseMailException;
use MartinPetricko\LaravelDatabaseMail\Facades\LaravelDatabaseMail;
use MartinPetricko\LaravelDatabaseMail\LaraveDatabaseMailEventServiceProvider;
use MartinPetricko\LaravelDatabaseMail\LaravelDatabaseMailServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();

        Exceptions::reportable(function (DatabaseMailException $e) {
            LaravelDatabaseMail::logException($e);
        });
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelDatabaseMailServiceProvider::class,
            LaraveDatabaseMailEventServiceProvider::class,
        ];
    }

    protected function setUpDatabase(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->string('locale')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }
}
