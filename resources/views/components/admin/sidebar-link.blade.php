@props([
    'href',
    'active' => false,
])

<a href="{{ $href }}"
   aria-current="{{ $active ? 'page' : 'false' }}"
   {{ $attributes->class([
       'group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-white',
       'bg-white text-slate-950 shadow-sm ring-1 ring-white' => $active,
       'text-slate-300 hover:bg-white/10 hover:text-white' => ! $active,
   ]) }}>
    <span class="{{ $active ? 'bg-slate-950' : 'bg-slate-500 group-hover:bg-white' }} h-2 w-2 rounded-full"
          aria-hidden="true"></span>
    <span>{{ $slot }}</span>
</a>
