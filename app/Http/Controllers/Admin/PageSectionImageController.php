<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageSectionImageController extends Controller
{
    public function __invoke(Request $request, Page $page): JsonResponse
    {
        $data = $request->validate(['search' => ['nullable', 'string', 'max:255'], 'page' => ['nullable', 'integer', 'min:1']]);
        $query = Media::query()->whereIn('mime_type', PageSection::IMAGE_MIMES);
        if (($data['search'] ?? '') !== '') {
            $query->where(fn ($query) => $query->where('title', 'like', '%'.$data['search'].'%')->orWhere('original_name', 'like', '%'.$data['search'].'%'));
        }
        $images = $query->orderByDesc('created_at')->orderByDesc('id')->paginate(24);

        return response()->json([
            'data' => $images->getCollection()->map(fn (Media $media) => [
                'id' => $media->id, 'name' => $media->title ?? $media->original_name,
                'url' => $media->url(), 'width' => $media->width, 'height' => $media->height,
            ])->values(),
            'current_page' => $images->currentPage(), 'last_page' => $images->lastPage(),
        ]);
    }
}
