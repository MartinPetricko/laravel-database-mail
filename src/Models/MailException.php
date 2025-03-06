<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $mail_template_id
 * @property array<string, mixed> $data
 * @property string $type
 * @property string $code
 * @property string $message
 * @property string $file
 * @property int $line
 * @property string $trace
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class MailException extends Model
{
    protected $fillable = [
        'mail_template_id',
        'data',
        'type',
        'code',
        'message',
        'file',
        'line',
        'trace',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
