<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use MartinPetricko\LaravelDatabaseMail\Facades\LaravelDatabaseMail;

/**
 * @property int $id
 * @property string $event
 * @property string $name
 * @property string $subject
 * @property string $body
 * @property array<mixed> $meta
 * @property array<string> $recipients
 * @property array<string> $attachments
 * @property ?string $delay
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection<int, MailException> $exceptions
 */
class MailTemplate extends Model
{
    protected $fillable = [
        'event',
        'name',
        'subject',
        'body',
        'meta',
        'recipients',
        'attachments',
        'delay',
        'is_active',
    ];

    protected $casts = [
        'meta' => 'array',
        'recipients' => 'array',
        'attachments' => 'array',
        'is_active' => 'bool',
    ];

    /** @return HasMany<MailException, $this> */
    public function exceptions(): HasMany
    {
        return $this->hasMany(LaravelDatabaseMail::getMailExceptionModel());
    }

    protected function subject(): Attribute
    {
        return Attribute::make(
            set: static fn (string $value): string => html_entity_decode($value),
        );
    }

    protected function body(): Attribute
    {
        return Attribute::make(
            set: static fn (string $value): string => html_entity_decode($value),
        );
    }
}
