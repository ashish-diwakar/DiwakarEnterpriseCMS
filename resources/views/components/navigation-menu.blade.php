@if ($nodes !== [])
    <nav aria-label="{{ $label }}" {{ $attributes }}>
        @include('frontend.partials.menu-branch', ['nodes' => $nodes])
    </nav>
@endif
