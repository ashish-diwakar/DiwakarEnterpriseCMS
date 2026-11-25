<ul class="flex flex-wrap items-start gap-x-6 gap-y-3">
    @foreach ($nodes as $node)
        <li class="min-w-0 break-words">
            @if ($node['url'] !== null)
                <a href="{{ $node['url'] }}" target="{{ $node['target'] }}" @if ($node['target'] === '_blank') rel="noopener noreferrer" @endif class="rounded text-slate-700 underline-offset-4 hover:underline focus:outline-none focus:ring-2 focus:ring-slate-500">{{ $node['label'] }}</a>
            @else
                <span class="font-medium text-slate-900">{{ $node['label'] }}</span>
            @endif
            @if ($node['children'] !== [])
                <div class="mt-3 border-l border-slate-200 pl-4">@include('frontend.partials.menu-branch', ['nodes' => $node['children']])</div>
            @endif
        </li>
    @endforeach
</ul>
