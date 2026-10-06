<x-admin-layout title="Website Settings"
                description="Manage public website information used by the CMS.">
    <form method="POST"
          action="{{ route('admin.settings.update') }}"
          class="space-y-6">
        @csrf
        @method('PATCH')

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

            <div class="mt-5 grid gap-5 md:grid-cols-2">
                <div>
                    <label for="public_email" class="block text-sm font-medium text-slate-700">Public email</label>
                    <input id="public_email"
                           name="public_email"
                           type="email"
                           maxlength="255"
                           value="{{ old('public_email', $settings->public_email) }}"
                           class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    @error('public_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="primary_phone" class="block text-sm font-medium text-slate-700">Primary phone</label>
                    <input id="primary_phone"
                           name="primary_phone"
                           type="text"
                           maxlength="50"
                           value="{{ old('primary_phone', $settings->primary_phone) }}"
                           class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    @error('primary_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="secondary_phone" class="block text-sm font-medium text-slate-700">Secondary phone</label>
                    <input id="secondary_phone"
                           name="secondary_phone"
                           type="text"
                           maxlength="50"
                           value="{{ old('secondary_phone', $settings->secondary_phone) }}"
                           class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    @error('secondary_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-slate-700">Address</label>
                    <textarea id="address"
                              name="address"
                              rows="4"
                              class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">{{ old('address', $settings->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
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

        <div class="flex justify-end">
            <button type="submit"
                    class="rounded-md bg-slate-950 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                Save Settings
            </button>
        </div>
    </form>
</x-admin-layout>
