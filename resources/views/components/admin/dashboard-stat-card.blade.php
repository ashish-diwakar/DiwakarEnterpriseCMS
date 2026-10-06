@props([
    'label',
    'value',
    'description' => null,
    'href' => null,
    'actionLabel' => null,
])

<article {{ $attributes->merge(['class' => 'rounded-lg border border-slate-200 bg-white p-5 shadow-sm']) }}>
    <div class="flex min-h-36 flex-col justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
            <p class="mt-3 text-3xl font-semibold tracking-normal text-slate-950">{{ $value }}</p>

            @if ($description)
                <p class="mt-2 text-sm text-slate-600">{{ $description }}</p>
            @endif
        </div>

        @if ($href && $actionLabel)
            <a href="{{ $href }}"
               class="inline-flex items-center self-start rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                {{ $actionLabel }}
            </a>
        @endif
    </div>
</article>
