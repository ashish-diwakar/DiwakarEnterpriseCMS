@php
    $user = auth()->user();
    $primaryRole = $user?->getRoleNames()->first() ?? 'No role';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <title>{{ $title }} | Diwakar Enterprise CMS</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="h-full font-sans text-slate-900 antialiased">
    <div x-data="{ sidebarOpen: false, accountOpen: false }"
         class="min-h-screen bg-slate-100">
        <div x-cloak
             x-show="sidebarOpen"
             x-transition.opacity
             class="fixed inset-0 z-30 bg-slate-950/50 lg:hidden"
             aria-hidden="true"
             x-on:click="sidebarOpen = false"></div>

        <div x-cloak
             x-show="sidebarOpen"
             x-transition
             class="fixed inset-y-0 left-0 z-40 w-72 lg:hidden">
            @include('admin.layouts.partials.sidebar', ['mobile' => true])
        </div>

        <div class="hidden lg:fixed lg:inset-y-0 lg:z-30 lg:flex lg:w-72 lg:flex-col">
            @include('admin.layouts.partials.sidebar', ['mobile' => false])
        </div>

        <div class="lg:pl-72">
            @include('admin.layouts.partials.header', [
                'user' => $user,
                'primaryRole' => $primaryRole,
            ])

            <main class="px-4 py-6 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-7xl">
                    <div class="mb-6 flex flex-col gap-4 border-b border-slate-200 pb-5 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h1 class="text-2xl font-semibold text-slate-950">{{ $title }}</h1>

                            @if ($description)
                                <p class="mt-1 max-w-3xl text-sm text-slate-600">{{ $description }}</p>
                            @endif
                        </div>

                        @isset($actions)
                            <div class="flex shrink-0 items-center gap-2">
                                {{ $actions }}
                            </div>
                        @endisset
                    </div>

                    @include('admin.layouts.partials.flash-messages')

                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</body>

</html>
