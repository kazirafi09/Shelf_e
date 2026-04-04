<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroSlideController extends Controller
{
    public function index()
    {
        $slides = HeroSlide::with('product')->orderBy('order', 'asc')->get();
        return view('admin.hero_slides.index', compact('slides'));
    }

    public function search(Request $request)
    {
        $q = trim($request->input('q', ''));

        $products = Product::query()
            ->when($q, fn($query) => $query->where('title', 'like', "%{$q}%")
                ->orWhere('author', 'like', "%{$q}%"))
            ->select('id', 'title', 'author', 'image_path')
            ->orderBy('title')
            ->limit(10)
            ->get()
            ->map(fn($p) => [
                'id'         => $p->id,
                'title'      => $p->title,
                'author'     => $p->author,
                'image_url'  => $p->image_path ? asset('storage/' . $p->image_path) : null,
            ]);

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'order'      => 'integer|min:0',
        ]);

        $product = Product::findOrFail($request->product_id);

        HeroSlide::create([
            'product_id' => $product->id,
            'image_path' => $product->image_path,
            'title'      => $product->title,
            'tag'        => $product->author,
            'order'      => $request->input('order', 0),
        ]);

        return redirect()->route('admin.hero-slides.index')->with('success', 'Slide added successfully!');
    }

    public function destroy(HeroSlide $heroSlide)
    {
        // Only delete the stored image if it is NOT linked to a product
        // (product-linked slides reuse the product's own image_path)
        if (!$heroSlide->product_id && $heroSlide->image_path) {
            if (Storage::disk('public')->exists($heroSlide->image_path)) {
                Storage::disk('public')->delete($heroSlide->image_path);
            }
        }

        $heroSlide->delete();

        return redirect()->route('admin.hero-slides.index')->with('success', 'Slide removed successfully!');
    }
}
