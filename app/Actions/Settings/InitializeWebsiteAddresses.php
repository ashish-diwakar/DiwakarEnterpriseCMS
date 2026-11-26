<?php

namespace App\Actions\Settings;

use App\Models\WebsiteSetting;
use App\Support\LegacyWebsiteAddresses;
use App\Support\WebsiteAddressesInput;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InitializeWebsiteAddresses
{
    public function __construct(private readonly SyncWebsiteAddresses $addresses) {}

    public function handle(bool $dryRun = true, ?array $reconciliation = null): array
    {
        if ($dryRun) {
            $owner = WebsiteSetting::query()->find(WebsiteSetting::SINGLETON_ID);

            return $owner ? $this->plan($owner, $reconciliation) : ['status' => 'missing', 'addresses' => [], 'issues' => []];
        }

        return DB::transaction(function () use ($reconciliation): array {
            $owner = WebsiteSetting::query()->whereKey(WebsiteSetting::SINGLETON_ID)->lockForUpdate()->firstOrFail();
            $plan = $this->plan($owner, $reconciliation);
            if ($plan['status'] === 'initialized') {
                return $plan;
            }
            if ($plan['issues'] !== []) {
                throw ValidationException::withMessages(['addresses' => $plan['issues']]);
            }
            $this->addresses->handle($owner, ['addresses' => $plan['addresses']]);
            $plan['status'] = 'initialized';

            return $plan;
        });
    }

    private function plan(WebsiteSetting $owner, ?array $reconciliation): array
    {
        if ($owner->addresses_initialized_at !== null) {
            return ['status' => 'initialized', 'already_initialized' => true, 'addresses' => [], 'issues' => []];
        }
        if ($owner->addresses()->exists()) {
            throw ValidationException::withMessages(['addresses' => 'Unexpected uninitialized address records exist. Preserve them for reconciliation review.']);
        }
        $plan = LegacyWebsiteAddresses::inspect($owner);
        if ($reconciliation !== null) {
            if (array_keys($reconciliation) !== ['addresses']) {
                throw ValidationException::withMessages(['addresses' => 'Reconciliation accepts only a complete addresses list.']);
            }
            $plan = WebsiteAddressesInput::validate($reconciliation);
            foreach ($plan['addresses'] as $row) {
                if (isset($row['id'])) {
                    throw ValidationException::withMessages(['addresses' => 'Initialization must not contain existing address IDs.']);
                }
                foreach (array_merge($row['emails'] ?? [], $row['phones'] ?? []) as $contact) {
                    if (isset($contact['id'])) {
                        throw ValidationException::withMessages(['addresses' => 'Initialization must not contain existing contact IDs.']);
                    }
                }
            }
            $plan['issues'] = [];
        }

        return ['status' => 'uninitialized', ...$plan];
    }
}
