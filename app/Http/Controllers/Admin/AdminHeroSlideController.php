<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminHeroSlideController extends Controller
{
    public function index(): View
    {
        $slides = HeroSlide::with('product')->orderBy('order')->orderBy('id')->get();

        return view('admin.hero-books.index', compact('slides'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'tag'        => 'nullable|string|max:50',
            'title'      => 'nullable|string|max:255',
            'order'      => 'required|integer|min:0',
        ]);

        if (! $request->filled('product_id') && ! $request->hasFile('image')) {
            return back()
                ->withErrors(['product_id' => 'Please select a book or upload a custom image.'])
                ->withInput();
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
            if (! in_array($file->getMimeType(), $allowedMimes)) {
                return back()
                    ->withErrors(['image' => 'The uploaded file must be a genuine image.'])
                    ->withInput();
            }
            $imagePath = $file->store('hero-slides', 'public');
        }

        HeroSlide::create([
            'product_id' => $request->input('product_id'),
            'image_path' => $imagePath,
            'tag'        => $request->input('tag'),
            'title'      => $request->input('title'),
            'order'      => $request->input('order'),
        ]);

        return redirect()->route('admin.hero-books.index')
            ->with('success', 'Hero slide added successfully.');
    }

    public function update(Request $request, HeroSlide $heroBook): RedirectResponse
    {
        $request->validate([
            'order' => 'required|integer|min:0',
            'tag'   => 'nullable|string|max:50',
            'title' => 'nullable|string|max:255',
        ]);

        $heroBook->update([
            'order' => $request->input('order'),
            'tag'   => $request->input('tag'),
            'title' => $request->input('title'),
        ]);

        return redirect()->route('admin.hero-books.index')
            ->with('success', 'Hero slide updated.');
    }

    public function destroy(HeroSlide $heroBook): RedirectResponse
    {
        if ($heroBook->image_path) {
            Storage::disk('public')->delete($heroBook->image_path);
        }

        $heroBook->delete();

        return redirect()->route('admin.hero-books.index')
            ->with('success', 'Hero slide removed.');
    }
}
