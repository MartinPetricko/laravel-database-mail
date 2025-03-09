<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use MartinPetricko\LaravelDatabaseMail\Facades\LaravelDatabaseMail;

use function Laravel\Prompts\multiselect;

class ImportMailTemplates extends Command
{
    protected $signature = 'mail:import
                    {--all : Import all mail templates to database}
                    {--path= : Path to mail templates json files}
                    {--search=* : The value being searched for}
                    {--replace=* : The replacement value that replaces found search values}';

    protected $description = 'Import mail templates from json file';

    public function handle(): void
    {
        /** @var string $path */
        $path = $this->option('path') ?: database_path('seeders/mails/');

        if (!File::exists($path) || !File::isDirectory($path)) {
            $this->components->error('Path to mail templates json files does not exist.');
            return;
        }

        $mailTemplates = [];
        foreach (File::files($path) as $file) {
            $mailTemplates[$file->getPathname()] = $file->getFilenameWithoutExtension();
        }

        if ($this->option('all')) {
            /** @var string[] $mailTemplates */
            $mailTemplates = array_keys($mailTemplates);
        } else {
            /** @var string[] $mailTemplates */
            $mailTemplates = multiselect(
                label: 'Which mail templates do you want to import?',
                options: $mailTemplates,
            );
        }

        $this->importMailTemplates($mailTemplates);

        $this->output->writeln('');
    }

    /**
     * @param string[] $mailTemplates
     */
    protected function importMailTemplates(array $mailTemplates): void
    {
        $this->components->info('Importing mail templates.');

        foreach ($mailTemplates as $mailTemplate) {
            $startTime = microtime(true);

            LaravelDatabaseMail::getMailTemplateModel()::create($this->getMailTemplateData($mailTemplate));

            $runTime = number_format((microtime(true) - $startTime) * 1000);

            $this->components->twoColumnDetail($mailTemplate, "<fg=gray>{$runTime} ms</> <fg=green;options=bold>DONE</>");
        }
    }

    /**
     * @return array<string, mixed>
     *
     * @throws FileNotFoundException
     */
    protected function getMailTemplateData(string $mailTemplate): array
    {
        /** @var string[] $search */
        $search = $this->option('search');

        /** @var string[] $replace */
        $replace = $this->option('replace');

        /** @var array<string, mixed> $mailTemplateData */
        $mailTemplateData = json_decode(str_replace($search, $replace, File::get($mailTemplate)), true);

        return $mailTemplateData;
    }
}
