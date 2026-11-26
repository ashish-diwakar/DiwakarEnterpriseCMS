<?php

namespace App\Console\Commands;

use App\Actions\Settings\InitializeWebsiteAddresses as InitializeAddresses;
use App\Support\SettingsReconciliationFile;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class InitializeWebsiteAddresses extends Command
{
    protected $signature = 'settings:initialize-addresses {--dry-run : Inspect without writes} {--reconcile= : Protected JSON file containing a complete addresses list}';

    protected $description = 'Explicitly initialize business addresses while preserving the complete original Address column.';

    public function handle(InitializeAddresses $action): int
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
            $this->error('Website Settings ID 1 is missing. Save General settings first.');

            return self::FAILURE;
        }
        if ($plan['already_initialized'] ?? false) {
            $this->info('Addresses are already initialized. Existing edits and intentional deletions were preserved.');

            return self::SUCCESS;
        }
        $this->info('Status: '.$plan['status'].'. Address entries: '.count($plan['addresses']).'.');
        foreach ($plan['issues'] as $issue) {
            $this->warn($issue);
        }
        $this->line('The complete legacy address was retained. Address and contact values are not printed.');

        return $plan['issues'] === [] ? self::SUCCESS : self::FAILURE;
    }
}
