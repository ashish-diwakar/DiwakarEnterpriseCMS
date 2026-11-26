<x-admin-layout title="Website Settings"
                description="Manage public website information used by the CMS.">
    <form method="POST"
          action="{{ route('admin.settings.update') }}"
          class="space-y-6"
          x-data="websiteContacts(@js($contactEditor))"
          x-on:input="dirty = true"
          x-on:submit="submit($event)">
        @csrf
        @method('PATCH')
        @if ($contactsEditable || $addressesEditable)
            <input type="hidden" name="collections_expected" value="1">
        @endif
        @if ($errors->any())
            <div role="alert" class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800"><p class="font-semibold">Please review the following:</p><ul class="mt-2 list-inside list-disc">@foreach ($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul></div>
        @endif

        <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-slate-950">General</h2>

            <div class="mt-5 grid gap-5 md:grid-cols-2">
                <div>
                    <label for="site_name" class="block text-sm font-medium text-slate-700">Site name</label>
                    <input id="site_name"
                           name="site_name"
                           type="text"
                           maxlength="120"
                           value="{{ old('site_name', $settings->site_name) }}"
                           required
                           class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    @error('site_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tagline" class="block text-sm font-medium text-slate-700">Tagline</label>
                    <input id="tagline"
                           name="tagline"
                           type="text"
                           maxlength="180"
                           value="{{ old('tagline', $settings->tagline) }}"
                           class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    @error('tagline')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-slate-950">Contact</h2>
            <p class="mt-3 text-sm text-slate-600">Choose at most one active primary per collection. Removing or deactivating a primary leaves no replacement. Up to 50 addresses and 100 contacts overall, within the safe form budget.</p>
            <p x-show="notice" x-text="notice" role="status" class="mt-3 rounded-md border border-slate-200 bg-slate-50 p-3 text-sm text-slate-700"></p>
            <x-admin.website-contacts :editable="$contactsEditable" :schema-ready="$contactsSchemaReady" :settings="$settings" />
            <x-admin.website-addresses :editable="$addressesEditable" :settings="$settings" />
            <p class="mt-4 text-xs text-slate-500">Phone numbers: one domestic/international number, 7–15 digits, optional leading +. Landline/Other may use ext 123. Country codes are not inferred.</p>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-slate-950">Social links</h2>

            <div class="mt-5 grid gap-5 md:grid-cols-2">
                <div>
                    <label for="facebook_url" class="block text-sm font-medium text-slate-700">Facebook URL</label>
                    <input id="facebook_url"
                           name="facebook_url"
                           type="url"
                           maxlength="2048"
                           value="{{ old('facebook_url', $settings->facebook_url) }}"
                           class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    @error('facebook_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="instagram_url" class="block text-sm font-medium text-slate-700">Instagram URL</label>
                    <input id="instagram_url"
                           name="instagram_url"
                           type="url"
                           maxlength="2048"
                           value="{{ old('instagram_url', $settings->instagram_url) }}"
                           class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    @error('instagram_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="linkedin_url" class="block text-sm font-medium text-slate-700">LinkedIn URL</label>
                    <input id="linkedin_url"
                           name="linkedin_url"
                           type="url"
                           maxlength="2048"
                           value="{{ old('linkedin_url', $settings->linkedin_url) }}"
                           class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    @error('linkedin_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="youtube_url" class="block text-sm font-medium text-slate-700">YouTube URL</label>
                    <input id="youtube_url"
                           name="youtube_url"
                           type="url"
                           maxlength="2048"
                           value="{{ old('youtube_url', $settings->youtube_url) }}"
                           class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    @error('youtube_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="x_twitter_url" class="block text-sm font-medium text-slate-700">X/Twitter URL</label>
                    <input id="x_twitter_url"
                           name="x_twitter_url"
                           type="url"
                           maxlength="2048"
                           value="{{ old('x_twitter_url', $settings->x_twitter_url) }}"
                           class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    @error('x_twitter_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-slate-950">Footer</h2>

            <div class="mt-5">
                <label for="footer_text" class="block text-sm font-medium text-slate-700">Footer text</label>
                <input id="footer_text"
                       name="footer_text"
                       type="text"
                       maxlength="500"
                       value="{{ old('footer_text', $settings->footer_text) }}"
                       class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                @error('footer_text')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </section>

        @if ($contactsEditable || $addressesEditable)
            <input type="hidden" name="contacts_version" value="{{ old('contacts_version', $settings->contacts_version ?? 0) }}">
            <noscript><p class="text-sm text-red-700">The collection editor requires JavaScript. Saving is blocked until it loads to protect existing records.</p></noscript>
            {{-- Keep this final marker after every collection input: PHP truncation must fail closed. --}}
            <input type="hidden" name="collections_complete" value="0" x-bind:value="1">
        @endif
        <dialog x-ref="contactRemove" x-on:close="pendingRemoval = null" aria-labelledby="contact-remove-title" class="m-auto max-w-lg rounded-xl border-0 bg-white p-6 text-slate-900 shadow-xl backdrop:bg-slate-950/60" style="width:calc(100% - 2rem)">
            <h3 id="contact-remove-title" class="text-lg font-semibold">Remove this entry?</h3>
            <p class="mt-3 text-sm leading-6 text-slate-600">This removes the entry from the form. Save Settings to apply. No primary replacement is selected automatically. Address contacts must be removed explicitly first.</p>
            <div class="mt-6 flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-5">
                <button type="button" autofocus x-on:click="$refs.contactRemove.close()" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm text-slate-700">Cancel</button>
                <button type="button" x-on:click="removeConfirmed()" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Remove entry</button>
            </div>
        </dialog>
        <div class="flex justify-end">
            <button type="submit"
                    class="rounded-md bg-slate-950 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                Save Settings
            </button>
        </div>
    </form>
</x-admin-layout>
