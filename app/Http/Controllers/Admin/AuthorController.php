<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        $query = Author::query();

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        $authors = $query->latest()->paginate(15)->withQueryString();

        return view('admin.authors.index', compact('authors'));
    }

    public function create()
    {
        return view('admin.authors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'bio'   => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
            if (! in_array($file->getMimeType(), $allowedMimes)) {
                throw ValidationException::withMessages([
                    'photo' => 'The uploaded file must be a genuine image.',
                ]);
            }
            $photoPath = $file->store('authors', 'public');
        }

        $author = Author::create([
            'name'       => $validated['name'],
            'slug'       => Str::slug($validated['name']) . '-' . substr(uniqid(), -5),
            'bio'        => $validated['bio'] ?? null,
            'photo_path' => $photoPath,
        ]);

        Cache::forget('homepage_data_v4');

        if ($request->wantsJson()) {
            return response()->json(['id' => $author->id, 'name' => $author->name], 201);
        }

        return redirect()->route('admin.authors.index')->with('success', 'Author created successfully.');
    }

    public function edit(Author $author)
    {
        return view('admin.authors.edit', compact('author'));
    }

    public function update(Request $request, Author $author)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'bio'   => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'bio'  => $validated['bio'] ?? null,
        ];

        if ($author->name !== $validated['name']) {
            $updateData['slug'] = Str::slug($validated['name']) . '-' . substr(uniqid(), -5);
        }

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
            if (! in_array($file->getMimeType(), $allowedMimes)) {
                throw ValidationException::withMessages([
                    'photo' => 'The uploaded file must be a genuine image.',
                ]);
            }

            if ($author->photo_path && Storage::disk('public')->exists($author->photo_path)) {
                Storage::disk('public')->delete($author->photo_path);
            }

            $updateData['photo_path'] = $file->store('authors', 'public');
        }

        $author->update($updateData);

        Cache::forget('homepage_data_v4');

        return redirect()->route('admin.authors.index')->with('success', "Author '{$author->name}' updated successfully.");
    }

    public function destroy(Author $author)
    {
        if ($author->photo_path && Storage::disk('public')->exists($author->photo_path)) {
            Storage::disk('public')->delete($author->photo_path);
        }

        $author->delete();

        Cache::forget('homepage_data_v4');

        return back()->with('success', "Author '{$author->name}' deleted.");
    }

    public function search(Request $request)
    {
        $authors = Author::where('name', 'LIKE', '%' . $request->input('q') . '%')
            ->select('id', 'name', 'slug', 'photo_path')
            ->limit(10)
            ->get();

        return response()->json($authors);
    }
}
