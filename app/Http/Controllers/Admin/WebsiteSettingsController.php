<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateWebsiteSettingsRequest;
use App\Models\WebsiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WebsiteSettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'settings' => WebsiteSetting::singleton(),
        ]);
    }

    public function update(UpdateWebsiteSettingsRequest $request): RedirectResponse
    {
        WebsiteSetting::saveSingleton($request->validated());

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', 'Website settings updated.');
    }
}
