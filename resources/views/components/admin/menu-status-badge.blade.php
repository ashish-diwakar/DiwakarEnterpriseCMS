@props(['active'])
<span aria-label="Configured status: {{ $active ? 'Active' : 'Inactive' }}"
      @class(['inline-flex shrink-0 items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset', 'bg-emerald-100 text-emerald-800 ring-emerald-200' => $active, 'bg-slate-200 text-slate-700 ring-slate-300' => ! $active])>
    {{ $active ? 'Active' : 'Inactive' }}
</span>
