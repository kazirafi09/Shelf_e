<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
            'product_id' => 'required|exists:products,id',
            'tag'        => 'nullable|string|max:50',
            'title'      => 'nullable|string|max:255',
            'order'      => 'required|integer|min:0',
        ]);

        HeroSlide::create([
            'product_id' => $request->input('product_id'),
            'image_path' => null,
            'tag'        => $request->input('tag'),
            'title'      => $request->input('title'),
            'order'      => $request->input('order'),
        ]);

        Cache::forget('hero_slides_v1');

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

        Cache::forget('hero_slides_v1');

        return redirect()->route('admin.hero-books.index')
            ->with('success', 'Hero slide updated.');
    }

    public function destroy(HeroSlide $heroBook): RedirectResponse
    {
        $heroBook->delete();

        Cache::forget('hero_slides_v1');

        return redirect()->route('admin.hero-books.index')
            ->with('success', 'Hero slide removed.');
    }
}
