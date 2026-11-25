<dialog x-ref="deleteDialog" aria-labelledby="menu-delete-heading" aria-describedby="menu-delete-message" class="m-auto w-full max-w-lg rounded-xl border-0 bg-white p-6 text-slate-900 shadow-xl backdrop:bg-slate-950/60" style="width:calc(100% - 2rem);max-height:calc(100% - 2rem)">
    <h2 id="menu-delete-heading" class="text-lg font-semibold">Delete navigation record?</h2>
    <p id="menu-delete-message" class="mt-3 break-words text-sm leading-6 text-slate-600">Permanently delete “<span x-text="deleteLabel"></span>”? Menus containing items and parent items containing children cannot be deleted.</p>
    <form method="POST" x-bind:action="deleteAction" class="mt-6 flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-5">
        @csrf @method('DELETE')
        <input type="hidden" name="version" x-bind:value="deleteVersion">
        <button type="button" autofocus x-on:click="$refs.deleteDialog.close()" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">Cancel</button>
        <button type="submit" x-bind:disabled="!deleteAction" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700">Delete</button>
    </form>
</dialog>
