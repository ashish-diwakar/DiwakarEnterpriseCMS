<x-admin-layout title="Create Page" description="Set the page identity and publication status.">
    <form method="POST" action="{{ route('admin.pages.store') }}" class="space-y-6"
          x-data="pageSlug" data-title="{{ old('title', '') }}" data-slug="{{ old('slug', '') }}">
        @csrf
        @include('admin.pages.partials.form', ['creating' => true])
    </form>
</x-admin-layout>
