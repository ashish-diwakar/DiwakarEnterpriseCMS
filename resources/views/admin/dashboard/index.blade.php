<x-admin-layout title="Dashboard"
                description="Overview of the currently available CMS administration areas.">
    <div class="space-y-6">
        <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-slate-950">CMS overview</h2>
            <p class="mt-2 max-w-3xl text-sm text-slate-600">
                Review the administration areas currently available to your account.
            </p>
        </section>

        @if ($summaryCards !== [])
            <section aria-labelledby="dashboard-summary-heading">
                <h2 id="dashboard-summary-heading" class="sr-only">Dashboard summary</h2>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($summaryCards as $card)
                        <x-admin.dashboard-stat-card :label="$card['label']"
                                                     :value="$card['value']"
                                                     :description="$card['description']"
                                                     :href="$card['href']"
                                                     :action-label="$card['actionLabel']" />
                    @endforeach
                </div>
            </section>
        @endif

    </div>
</x-admin-layout>
