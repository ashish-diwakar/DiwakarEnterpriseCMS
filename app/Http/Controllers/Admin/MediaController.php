<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Media\DeleteMedia;
use App\Actions\Media\StoreMedia;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMediaRequest;
use App\Http\Requests\Admin\UpdateMediaRequest;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class MediaController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:255'], 'type' => ['nullable', 'in:all,images,documents']]);
        $query = Media::query();
        if (($filters['search'] ?? '') !== '') {
            $query->where(function ($query) use ($filters): void {
                $query->where('original_name', 'like', '%'.$filters['search'].'%')
                    ->orWhere('title', 'like', '%'.$filters['search'].'%');
            });
        }
        if (($filters['type'] ?? '') === 'images') {
            $query->whereIn('mime_type', ['image/jpeg', 'image/png', 'image/webp']);
        } elseif (($filters['type'] ?? '') === 'documents') {
            $query->where('mime_type', 'application/pdf');
        }

        return view('admin.media.index', [
            'mediaItems' => $query->orderByDesc('created_at')->orderByDesc('id')->paginate(24)->appends($filters),
            'filters' => $filters,
        ]);
    }

    public function store(StoreMediaRequest $request, StoreMedia $storeMedia): RedirectResponse
    {
        $uploaded = 0;
        try {
            foreach ($request->validated('files') as $file) {
                $storeMedia->handle($file);
                $uploaded++;
            }
        } catch (ValidationException $exception) {
            throw ValidationException::withMessages(['files.'.$uploaded => 'The image dimensions could not be read.']);
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('admin.media.index')->with('error', "Upload stopped after {$uploaded} successful file(s). Previously uploaded files remain available. Please retry only the remaining files.");
        }

        return redirect()->route('admin.media.index')->with('status', "{$uploaded} media file(s) uploaded successfully.");
    }

    public function update(UpdateMediaRequest $request, Media $media): RedirectResponse|JsonResponse
    {
        $media->update($request->safe()->only('title'));

        if ($request->expectsJson()) {
            return response()->json(['title' => $media->title, 'display_name' => $media->title ?? $media->original_name]);
        }

        return redirect()->route('admin.media.index')->with('status', 'Display name updated successfully.');
    }

    public function destroy(Media $media, DeleteMedia $deleteMedia): RedirectResponse
    {
        try {
            $deleteMedia->handle($media);
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('admin.media.index')->with('error', 'The media could not be deleted. Please try again.');
        }

        return redirect()->route('admin.media.index')->with('status', 'Media deleted successfully.');
    }
}
