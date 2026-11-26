@props(['group', 'nested' => false])
@php
    $list = $nested ? 'address.'.$group : $group;
    $base = $nested ? "'addresses['+addressIndex+'][$group]'" : "'$group'";
    $error = $nested ? "'addresses.'+addressIndex+'.$group.'+index" : "'$group.'+index";
    $argument = $nested ? ', address' : '';
    $heading = $group === 'emails' ? 'Email addresses' : 'Phone numbers';
@endphp
<section class="mt-5 rounded-lg border border-slate-200 p-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h4 class="font-semibold text-slate-950">{{ $heading }}</h4>
        <div class="flex flex-wrap gap-2">
            <button type="button" x-on:click="primary('{{ $group }}', null{{ $argument }})" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">Clear primary</button>
            <button type="button" x-on:click="add('{{ $group }}'{{ $argument }})" class="rounded-md bg-slate-950 px-3 py-2 text-sm font-medium text-white hover:bg-slate-800">{{ $group === 'emails' ? 'Add Email' : 'Add Phone' }}</button>
        </div>
    </div>
    <p x-show="{{ $list }}.length === 0" class="mt-4 text-sm text-slate-500">No {{ strtolower($heading) }}. An empty collection is permitted.</p>
    <div class="mt-4 space-y-4">
        <template x-for="(row, index) in {{ $list }}" :key="row._key">
            <article class="min-w-0 rounded-lg border p-4" x-bind:class="row.is_active ? 'border-slate-200 bg-white' : 'border-slate-300 bg-slate-50'">
                <input type="hidden" x-bind:name="{{ $base }}+'['+index+'][id]'" x-bind:value="row.id">
                <div class="grid min-w-0 gap-4 md:grid-cols-2">
                    <div class="min-w-0">
                        <label x-bind:for="row._key+'-label'" class="block text-sm font-medium text-slate-700">Display label (optional)</label>
                        <input type="text" maxlength="120" x-bind:id="row._key+'-label'" x-bind:name="{{ $base }}+'['+index+'][label]'" x-model="row.label" class="mt-1 w-full rounded-md border-slate-300">
                        <p x-show="error({{ $error }}+'.label')" x-text="error({{ $error }}+'.label')" class="mt-1 text-sm text-red-600" role="alert"></p>
                    </div>
                    <div class="min-w-0">
                        <label x-bind:for="row._key+'-value'" class="block text-sm font-medium text-slate-700">{{ $group === 'emails' ? 'Email address' : 'Phone number' }}</label>
                        <input type="{{ $group === 'emails' ? 'email' : 'tel' }}" maxlength="{{ $group === 'emails' ? 255 : 80 }}" required x-bind:id="row._key+'-value'" x-bind:name="{{ $base }}+'['+index+'][value]'" x-model="row.value" class="mt-1 w-full rounded-md border-slate-300">
                        <p x-show="error({{ $error }}+'.value')" x-text="error({{ $error }}+'.value')" class="mt-1 text-sm text-red-600" role="alert"></p>
                    </div>
                    @if ($group === 'phones')
                        <div>
                            <label x-bind:for="row._key+'-subtype'" class="block text-sm font-medium text-slate-700">Phone type</label>
                            <select x-bind:id="row._key+'-subtype'" x-bind:name="{{ $base }}+'['+index+'][subtype]'" x-model="row.subtype" class="mt-1 w-full rounded-md border-slate-300">
                                @foreach (\App\Support\ContactValue::PHONE_TYPES as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                            </select>
                            <p x-show="error({{ $error }}+'.subtype')" x-text="error({{ $error }}+'.subtype')" class="mt-1 text-sm text-red-600" role="alert"></p>
                        </div>
                    @endif
                </div>
                <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-4">
                        <input type="hidden" x-bind:name="{{ $base }}+'['+index+'][is_primary]'" x-bind:value="row.is_primary ? 1 : 0">
                        <label class="flex items-center gap-2 text-sm text-slate-700"><input type="radio" x-bind:name="{{ $nested ? "address._key+'-$group-primary-picker'" : "'default-$group-primary-picker'" }}" x-bind:checked="row.is_primary" x-bind:disabled="!row.is_active" x-on:change="primary('{{ $group }}', row._key{{ $argument }})" class="border-slate-300 text-slate-950">Primary</label>
                        <input type="hidden" x-bind:name="{{ $base }}+'['+index+'][is_active]'" x-bind:value="row.is_active ? 1 : 0">
                        <label class="flex items-center gap-2 text-sm text-slate-700"><input type="checkbox" x-model="row.is_active" x-on:change="activeChanged(row)" class="rounded border-slate-300 text-slate-950">Active</label>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold" x-bind:class="row.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700'" x-text="row.is_active ? 'Active' : 'Inactive'"></span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" x-bind:disabled="index === 0" x-on:click="move('{{ $group }}', row._key, -1{{ $argument }})" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 disabled:opacity-40">Move up</button>
                        <button type="button" x-bind:disabled="index === {{ $list }}.length-1" x-on:click="move('{{ $group }}', row._key, 1{{ $argument }})" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 disabled:opacity-40">Move down</button>
                        <button type="button" x-on:click="requestRemove('{{ $group }}', row._key{{ $argument }})" class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700 hover:bg-red-100">Remove</button>
                    </div>
                </div>
                <p x-show="error({{ $error }}+'.is_primary')" x-text="error({{ $error }}+'.is_primary')" class="mt-2 text-sm text-red-600" role="alert"></p>
                <p x-show="error({{ $error }}+'.id')" x-text="error({{ $error }}+'.id')" class="mt-2 text-sm text-red-600" role="alert"></p>
            </article>
        </template>
    </div>
</section>

