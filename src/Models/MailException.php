<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

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
    use MassPrunable;

    public const EXCEPTION_PREVIEW_PADDING = 5;

    protected $fillable = [
        'mail_template_id',
        'data',
        'type',
        'code',
        'message',
        'file',
        'line',
        'trace',
        'preview',
    ];

    protected $casts = [
        'data' => 'array',
        'preview' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(static function (self $exception) {
            if (!is_readable($exception->file)) {
                $exception->preview = [];
                return;
            }

            $lines = File::lines($exception->file);

            /** @var array<int> $range */
            $range = range(
                max(0, $exception->line - self::EXCEPTION_PREVIEW_PADDING - 1),
                min($lines->count() - 1, $exception->line + self::EXCEPTION_PREVIEW_PADDING - 1)
            );

            $exception->preview = $lines->only($range)->all();
        });
    }

    /** @return BelongsTo<MailTemplate, $this> */
    public function template(): BelongsTo
    {
        return $this->belongsTo(MailTemplate::class);
    }

    /** @return Builder<MailException> */
    public function prunable(): Builder
    {
        return static::where('created_at', '<=', config('database-mail.prune_exceptions_period'));
    }
}
