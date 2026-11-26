<?php

namespace App\Actions\Settings;

use App\Models\WebsiteSetting;
use App\Support\LegacyWebsiteContacts;
use App\Support\WebsiteContactsInput;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InitializeWebsiteContacts
{
    public function __construct(private readonly SyncWebsiteContacts $contacts) {}

    public function handle(bool $dryRun = true, ?array $reconciliation = null): array
    {
        // A dry run is genuinely read-only: no row locks, inserts or marker writes.
        if ($dryRun) {
            $owner = WebsiteSetting::query()->find(WebsiteSetting::SINGLETON_ID);

            return $owner ? $this->plan($owner, $reconciliation) : ['status' => 'missing', 'emails' => [], 'phones' => [], 'issues' => [], 'duplicates' => 0];
        }

        return DB::transaction(function () use ($reconciliation): array {
            $owner = WebsiteSetting::query()->whereKey(WebsiteSetting::SINGLETON_ID)->lockForUpdate()->firstOrFail();
            $plan = $this->plan($owner, $reconciliation);
            if ($plan['status'] === 'initialized') {
                return $plan;
            }
            if ($plan['issues'] !== []) {
                throw ValidationException::withMessages(['contacts' => $plan['issues']]);
            }
            $this->contacts->handle($owner, Arr::only($plan, ['emails', 'phones']));
            $plan['status'] = 'initialized';

            return $plan;
        });
    }

    private function plan(WebsiteSetting $owner, ?array $reconciliation): array
    {
        if ($owner->contacts_initialized_at !== null) {
            return ['status' => 'initialized', 'already_initialized' => true, 'emails' => [], 'phones' => [], 'issues' => [], 'duplicates' => 0];
        }
        if ($owner->contacts()->exists()) {
            throw ValidationException::withMessages(['contacts' => 'Uninitialized normalized rows already exist. Preserve them and request reconciliation review.']);
        }
        $plan = LegacyWebsiteContacts::inspect($owner);
        if ($reconciliation !== null) {
            if (array_diff(array_keys($reconciliation), ['emails', 'phones']) !== []) {
                throw ValidationException::withMessages(['contacts' => 'Reconciliation accepts only complete emails and phones collections.']);
            }
            $validated = WebsiteContactsInput::validate($reconciliation);
            foreach (array_merge($validated['emails'], $validated['phones']) as $row) {
                if (isset($row['id'])) {
                    throw ValidationException::withMessages(['contacts' => 'Initialization must not contain database contact IDs.']);
                }
            }
            $plan = [...$validated, 'issues' => [], 'duplicates' => 0];
        }

        return ['status' => 'uninitialized', ...$plan];
    }
}
