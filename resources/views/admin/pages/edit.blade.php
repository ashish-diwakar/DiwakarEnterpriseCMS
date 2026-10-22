<x-admin-layout title="Edit Page" description="Update the page identity and publication status.">
    <div class="mb-6"><a href="{{ route('admin.pages.sections.index', $page) }}" class="inline-flex rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Manage Sections</a></div>
    <form method="POST" action="{{ route('admin.pages.update', $page) }}" class="space-y-6">
        @csrf
        @method('PATCH')
        @include('admin.pages.partials.form', ['creating' => false])
    </form>
</x-admin-layout>
