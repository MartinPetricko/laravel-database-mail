<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MartinPetricko\LaravelDatabaseMail\Models\MailTemplate;

/** @extends Factory<MailTemplate> */
class MailTemplateFactory extends Factory
{
    protected $model = MailTemplate::class;

    public function definition(): array
    {
        return [

        ];
    }
}
