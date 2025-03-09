<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use JsonException;
use MartinPetricko\LaravelDatabaseMail\Facades\LaravelDatabaseMail;

use MartinPetricko\LaravelDatabaseMail\Models\MailTemplate;
use function Laravel\Prompts\multisearch;

class ExportMailTemplates extends Command
{
    protected $signature = 'mail:export
                    {--all : Export all mail templates from database}
                    {--path= : Path to export mail templates to}';

    protected $description = 'Export mail templates to json file';

    public function handle(): void
    {
        if ($this->option('all')) {
            $mailTemplates = LaravelDatabaseMail::getMailTemplateModel()::all();
        } else {
            $mailTemplates = LaravelDatabaseMail::getMailTemplateModel()::findMany(
                multisearch(
                    label: 'Which mail templates do you want to export?',
                    options: function (string $value) {
                        /** @var array<int, string> $mailTemplates */
                        $mailTemplates = LaravelDatabaseMail::getMailTemplateModel()::whereLike('name', '%' . $value . '%')->pluck('name', 'id')->all();
                        return $mailTemplates;
                    },
                ),
            );
        }

        /** @var string $path */
        $path = $this->option('path') ?: database_path('seeders/mails/');

        $this->createMailTemplatesDirectory($path);

        $this->exportMailTemplates($mailTemplates, $path);

        $this->output->writeln('');
    }

    protected function createMailTemplatesDirectory(string $path): void
    {
        $this->components->info('Creating mails directory.');

        $startTime = microtime(true);

        File::ensureDirectoryExists($path);

        $runTime = number_format((microtime(true) - $startTime) * 1000);

        $this->components->twoColumnDetail($path, "<fg=gray>{$runTime} ms</> <fg=green;options=bold>DONE</>");
    }

    /**
     * @param Collection<int, MailTemplate> $mailTemplates
     *
     * @throws JsonException
     */
    protected function exportMailTemplates(Collection $mailTemplates, string $path): void
    {
        $this->components->info('Exporting mail templates.');

        foreach ($mailTemplates as $mailTemplate) {
            $startTime = microtime(true);

            $filePath = $path . Str::kebab($mailTemplate->name) . '.json';

            $mailTemplateData = array_diff_key($mailTemplate->toArray(), array_flip(['id', 'created_at', 'updated_at']));

            File::put($filePath, json_encode($mailTemplateData, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

            $runTime = number_format((microtime(true) - $startTime) * 1000);

            $this->components->twoColumnDetail($filePath, "<fg=gray>{$runTime} ms</> <fg=green;options=bold>DONE</>");
        }
    }
}
