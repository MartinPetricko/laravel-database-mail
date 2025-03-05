<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Example\Models;

use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property ?string $locale
 * @property ?string $remember_token
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class User extends Model implements HasLocalePreference
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function preferredLocale(): ?string
    {
        return $this->locale;
    }
}
