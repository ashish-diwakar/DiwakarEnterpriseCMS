<x-admin-layout title="Edit Page" description="Update the page identity and publication status.">
    <form method="POST" action="{{ route('admin.pages.update', $page) }}" class="space-y-6">
        @csrf
        @method('PATCH')
        @include('admin.pages.partials.form', ['creating' => false])
    </form>
</x-admin-layout>
