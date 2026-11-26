@props(['editable', 'schemaReady', 'settings'])
<h3 class="mt-5 font-semibold text-slate-950">Default Website Contacts</h3>
<p class="mt-2 text-sm text-slate-600">Website-wide contacts remain independent of every business address.</p>
@if (! $editable)
    <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-slate-800">
        <p class="font-medium">{{ $schemaReady ? 'Legacy contact initialization requires manual review.' : 'Install all three Settings collection migrations first.' }}</p>
        <p class="mt-2">Existing values are preserved. Other initialized collections, General, Social Links and Footer remain manageable.</p>
        <dl class="mt-3 space-y-2">
            @foreach (['public_email' => 'Public email (preserved)', 'primary_phone' => 'Primary phone (preserved)', 'secondary_phone' => 'Secondary phone (preserved)'] as $field => $label)
                @if ($settings->$field)<div><dt class="font-medium">{{ $label }}</dt><dd class="break-words">{{ $settings->$field }}</dd></div>@endif
            @endforeach
        </dl>
    </div>
@else
    <input type="hidden" name="contacts_present" value="1">
    <input type="hidden" name="contacts_ready" value="0" x-bind:value="1">
    <input type="hidden" name="emails_count" x-bind:value="emails.length">
    <input type="hidden" name="phones_count" x-bind:value="phones.length">
    <x-admin.website-contact-rows group="emails" />
    <x-admin.website-contact-rows group="phones" />
@endif

