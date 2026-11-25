@if ($errors->any())
    <div role="alert" class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
        <p class="font-semibold">Please review the following:</p>
        <ul class="mt-2 list-inside list-disc">@foreach ($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul>
    </div>
@endif
