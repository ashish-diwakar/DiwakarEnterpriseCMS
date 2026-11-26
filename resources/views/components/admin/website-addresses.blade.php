@props(['editable', 'settings'])
<section class="mt-8 border-t border-slate-200 pt-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h3 class="font-semibold text-slate-950">Business Addresses</h3>
        @if ($editable)<button type="button" x-on:click="addAddress()" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Add Address</button>@endif
    </div>
    <p class="mt-2 text-sm text-slate-600">Each address has independent email and phone contacts. An inactive address hides its contacts publicly without deleting them.</p>
    @if (! $editable)
        <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-slate-800">
            <p class="font-medium">Install the migrations and review legacy address initialization before editing addresses.</p>
            <p class="mt-3 whitespace-pre-line break-words">{{ $settings->address }}</p>
        </div>
    @else
        <input type="hidden" name="addresses_present" value="1">
        <input type="hidden" name="addresses_ready" value="0" x-bind:value="1">
        <input type="hidden" name="addresses_count" x-bind:value="addresses.length">
        <input type="hidden" name="removed_addresses_count" x-bind:value="removedAddresses.length">
        <p x-show="addresses.length === 0" class="mt-4 text-sm text-slate-500">No business addresses. An empty collection is permitted.</p>
        <div class="mt-4 space-y-5">
            <template x-for="(address, addressIndex) in addresses" :key="address._key">
                <article class="min-w-0 rounded-lg border p-4 sm:p-5" x-bind:class="address.is_active ? 'border-slate-200 bg-white' : 'border-slate-300 bg-slate-50'">
                    <input type="hidden" x-bind:name="'addresses['+addressIndex+'][id]'" x-bind:value="address.id">
                    <input type="hidden" x-bind:name="'addresses['+addressIndex+'][is_primary]'" x-bind:value="address.is_primary ? 1 : 0">
                    <input type="hidden" x-bind:name="'addresses['+addressIndex+'][is_active]'" x-bind:value="address.is_active ? 1 : 0">
                    <input type="hidden" x-bind:name="'addresses['+addressIndex+'][contacts_present]'" value="1">
                    <input type="hidden" x-bind:name="'addresses['+addressIndex+'][contacts_ready]'" value="0" x-bind:value="1">
                    <input type="hidden" x-bind:name="'addresses['+addressIndex+'][emails_count]'" x-bind:value="address.emails.length">
                    <input type="hidden" x-bind:name="'addresses['+addressIndex+'][phones_count]'" x-bind:value="address.phones.length">
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach (['label' => 'Address label', 'address_line_1' => 'Address line 1', 'address_line_2' => 'Address line 2', 'city' => 'City / locality', 'state' => 'State / region', 'postal_code' => 'Postal code', 'country' => 'Country'] as $field => $label)
                            <div class="min-w-0 {{ $field === 'address_line_1' ? 'md:col-span-2' : '' }}">
                                <label x-bind:for="address._key+'-{{ $field }}'" class="block text-sm font-medium text-slate-700">{{ $label }}{{ in_array($field, ['label', 'address_line_1']) ? ' *' : '' }}</label>
                                @if ($field === 'address_line_1')
                                    <textarea rows="3" maxlength="2000" required x-bind:id="address._key+'-{{ $field }}'" x-bind:name="'addresses['+addressIndex+'][{{ $field }}]'" x-model="address.{{ $field }}" class="mt-1 w-full rounded-md border-slate-300"></textarea>
                                @else
                                    <input type="text" maxlength="{{ $field === 'address_line_2' ? 255 : ($field === 'postal_code' ? 32 : 120) }}" @required($field === 'label') x-bind:id="address._key+'-{{ $field }}'" x-bind:name="'addresses['+addressIndex+'][{{ $field }}]'" x-model="address.{{ $field }}" class="mt-1 w-full rounded-md border-slate-300">
                                @endif
                                <p x-show="error('addresses.'+addressIndex+'.{{ $field }}')" x-text="error('addresses.'+addressIndex+'.{{ $field }}')" class="mt-1 text-sm text-red-600" role="alert"></p>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-4">
                            <label class="flex items-center gap-2 text-sm"><input type="radio" name="address_primary_picker" x-bind:checked="address.is_primary" x-bind:disabled="!address.is_active" x-on:change="primary('addresses', address._key)">Primary address</label>
                            <label class="flex items-center gap-2 text-sm"><input type="checkbox" x-model="address.is_active" x-on:change="activeChanged(address)">Active</label>
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold" x-bind:class="address.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700'" x-text="address.is_active ? 'Active' : 'Inactive'"></span>
                            <button type="button" x-on:click="primary('addresses', null)" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm">Clear primary</button>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" x-bind:disabled="addressIndex === 0" x-on:click="move('addresses', address._key, -1)" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm disabled:opacity-40">Move up</button>
                            <button type="button" x-bind:disabled="addressIndex === addresses.length-1" x-on:click="move('addresses', address._key, 1)" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm disabled:opacity-40">Move down</button>
                            <button type="button" x-on:click="requestRemove('addresses', address._key)" class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">Remove address</button>
                        </div>
                    </div>
                    @foreach (['id', 'is_primary', 'contacts_ready', 'emails_count', 'phones_count', 'emails', 'phones'] as $field)
                        <p x-show="error('addresses.'+addressIndex+'.{{ $field }}')" x-text="error('addresses.'+addressIndex+'.{{ $field }}')" class="mt-2 text-sm text-red-600" role="alert"></p>
                    @endforeach
                    <details class="mt-4 rounded-lg border border-slate-200 p-4" x-bind:open="address.expanded" x-on:toggle="address.expanded = $event.target.open">
                        <summary class="cursor-pointer font-medium text-slate-800">Email and phone contacts for this address</summary>
                        <p class="mt-3 text-sm text-slate-600">Optional. Up to 10 emails and 10 phones for this address. Its primary contacts do not change default website contacts.</p>
                        <x-admin.website-contact-rows group="emails" :nested="true" />
                        <x-admin.website-contact-rows group="phones" :nested="true" />
                    </details>
                </article>
            </template>
        </div>
        <template x-for="(removed, removedIndex) in removedAddresses" :key="removed.id">
            <div>
                <input type="hidden" x-bind:name="'removed_addresses['+removedIndex+'][id]'" x-bind:value="removed.id">
                <template x-for="(contactId, contactIndex) in removed.contact_ids" :key="contactId">
                    <input type="hidden" x-bind:name="'removed_addresses['+removedIndex+'][contact_ids]['+contactIndex+']'" x-bind:value="contactId">
                </template>
            </div>
        </template>
    @endif
</section>

