<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use MartinPetricko\LaravelDatabaseMail\Database\Factories\MailTemplateFactory;

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
 */
class MailTemplate extends Model
{
    /** @use HasFactory<MailTemplateFactory> */
    use HasFactory;

    protected $fillable = [
        'event',
        'name',
        'subject',
        'body',
        'meta',
        'recipients',
        'attachments',
        'delay',
    ];

    protected $casts = [
        'meta' => 'array',
        'recipients' => 'array',
        'attachments' => 'array',
        'is_active' => 'bool',
    ];
}
