@props(['assessment', 'unassigned' => false])
<div class="mt-3 text-sm">
    <p><span class="font-medium text-slate-700">Public visibility:</span>
        @if ($assessment['visible'])
            <span class="font-medium text-emerald-800">{{ $unassigned ? 'Eligible by identifier' : 'Eligible' }}</span>
        @else
            <span class="font-medium text-amber-800">Hidden</span>
        @endif
    </p>
    @if ($assessment['reasons'] !== [])
        <ul class="mt-1 list-inside list-disc space-y-1 text-slate-600">
            @foreach ($assessment['reasons'] as $reason)<li>{{ $reason }}</li>@endforeach
        </ul>
    @endif
</div>
