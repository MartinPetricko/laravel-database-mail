<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    use Prunable;

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

    /** @return BelongsTo<MailTemplate, $this> */
    public function template(): BelongsTo
    {
        return $this->belongsTo(MailTemplate::class);
    }

    /** @return Builder<MailException> */
    public function prunable(): Builder
    {
        return static::where('created_at', '<', config('database-mail.prune_exceptions_period'));
    }
}
