<?php

namespace App\Console\Commands;

use App\Actions\Settings\InitializeWebsiteContacts as InitializeContacts;
use App\Support\SettingsReconciliationFile;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class InitializeWebsiteContacts extends Command
{
    protected $signature = 'settings:initialize-contacts {--dry-run : Inspect without changing records} {--reconcile= : Protected JSON file containing complete emails and phones lists}';

    protected $description = 'Explicitly initialize default contacts while retaining every legacy contact column.';

    public function handle(InitializeContacts $action): int
    {
        try {
            $plan = $action->handle((bool) $this->option('dry-run'), SettingsReconciliationFile::read($this->option('reconcile')));
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $messages) {
                foreach ($messages as $message) {
                    $this->error($message);
                }
            }

            return self::FAILURE;
        }
        if ($plan['status'] === 'missing') {
            $this->error('Website Settings ID 1 is missing. Save General settings before initializing contacts.');

            return self::FAILURE;
        }
        if ($plan['already_initialized'] ?? false) {
            $this->info('Contacts are already initialized. Existing edits and intentional deletions were preserved.');

            return self::SUCCESS;
        }
        $this->info('Status: '.$plan['status'].'. Email entries: '.count($plan['emails']).'. Phone entries: '.count($plan['phones']).'. Duplicates collapsed: '.$plan['duplicates'].'.');
        foreach ($plan['issues'] as $issue) {
            $this->warn($issue);
        }
        $this->line('Legacy columns were not modified. Contact values are not printed.');

        return $plan['issues'] === [] ? self::SUCCESS : self::FAILURE;
    }
}
