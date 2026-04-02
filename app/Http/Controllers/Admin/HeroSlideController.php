<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroSlideController extends Controller
{
    public function index()
    {
        $slides = HeroSlide::orderBy('order', 'asc')->get();
        return view('admin.hero_slides.index', compact('slides'));
    }

    public function create()
    {
        return view('admin.hero_slides.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048|dimensions:min_width=100,min_height=100',
            'tag' => 'nullable|string|max:20',
            'title' => 'nullable|string|max:50',
            'order' => 'integer'
        ]);

        $imagePath = $request->file('image')->store('hero_slides', 'public');

        HeroSlide::create([
            'image_path' => $imagePath,
            'tag' => $request->tag,
            'title' => $request->title,
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('admin.hero-slides.index')->with('success', 'Slide created successfully!');
    }

    public function edit(HeroSlide $heroSlide)
    {
        return view('admin.hero_slides.form', compact('heroSlide'));
    }

    public function update(Request $request, HeroSlide $heroSlide)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048|dimensions:min_width=100,min_height=100',
            'tag' => 'nullable|string|max:20',
            'title' => 'nullable|string|max:50',
            'order' => 'integer'
        ]);

        $data = [
            'tag' => $request->tag,
            'title' => $request->title,
            'order' => $request->order ?? 0,
        ];

        // Handle image replacement
        if ($request->hasFile('image')) {
            // Delete old image
            if (Storage::disk('public')->exists($heroSlide->image_path)) {
                Storage::disk('public')->delete($heroSlide->image_path);
            }
            // Save new image
            $data['image_path'] = $request->file('image')->store('hero_slides', 'public');
        }

        $heroSlide->update($data);

        return redirect()->route('admin.hero-slides.index')->with('success', 'Slide updated successfully!');
    }

    public function destroy(HeroSlide $heroSlide)
    {
        if (Storage::disk('public')->exists($heroSlide->image_path)) {
            Storage::disk('public')->delete($heroSlide->image_path);
        }
        $heroSlide->delete();

        return redirect()->route('admin.hero-slides.index')->with('success', 'Slide deleted successfully!');
    }
}
