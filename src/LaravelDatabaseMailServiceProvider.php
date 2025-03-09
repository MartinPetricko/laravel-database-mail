<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail;

use MartinPetricko\LaravelDatabaseMail\Commands\ExportMailTemplates;
use MartinPetricko\LaravelDatabaseMail\Commands\ImportMailTemplates;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelDatabaseMailServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-database-mail')
            ->hasConfigFile()
            ->hasCommands(ExportMailTemplates::class, ImportMailTemplates::class)
            ->hasMigrations('create_mail_templates_table', 'create_mail_exceptions_table');
    }

    public function registeringPackage(): void
    {
        if (config('database-mail.register_event_listener')) {
            $this->app->register(LaraveDatabaseMailEventServiceProvider::class);
        }
    }
}
