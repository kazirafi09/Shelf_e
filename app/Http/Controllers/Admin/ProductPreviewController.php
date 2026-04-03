<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductPreview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductPreviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'file'       => 'required|file|max:51200|mimes:jpeg,png,jpg,webp,mp4,mov,webm',
            'sort_order' => 'nullable|integer|min:0|max:255',
        ]);

        $file = $request->file('file');
        $mime = $file->getMimeType();

        $imageTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $videoTypes = ['video/mp4', 'video/quicktime', 'video/webm'];

        if (in_array($mime, $imageTypes)) {
            $type = 'image';
        } elseif (in_array($mime, $videoTypes)) {
            $type = 'video';
        } else {
            throw ValidationException::withMessages([
                'file' => 'The uploaded file must be an image or video.',
            ]);
        }

        $path = $file->store('previews', 'public');

        $product->previews()->create([
            'type'       => $type,
            'path'       => $path,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return back()->with('success', 'Preview added successfully.');
    }

    public function destroy(ProductPreview $preview)
    {
        if (Storage::disk('public')->exists($preview->path)) {
            Storage::disk('public')->delete($preview->path);
        }

        $preview->delete();

        return back()->with('success', 'Preview deleted.');
    }
}
