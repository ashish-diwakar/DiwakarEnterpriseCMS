<x-admin-layout title="Edit Section" description="Edit content without changing its owning Page or type.">
    <p class="mb-5 break-words text-sm text-slate-600">Page: {{ $page->title }}</p>
    <form method="POST" action="{{ route('admin.pages.sections.update', [$page, $section]) }}">
        @csrf
        @method('PATCH')
        @include('admin.page-sections.partials.form')
    </form>
</x-admin-layout>
