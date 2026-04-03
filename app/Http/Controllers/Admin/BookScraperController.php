<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\BookScraperService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookScraperController extends Controller
{
    public function __construct(private readonly BookScraperService $scraper) {}

    public function index()
    {
        return view('admin.scraper.index');
    }

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2|max:255',
        ]);

        try {
            $results = $this->scraper->searchByTitle($request->input('query'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 502);
        }

        return response()->json($results);
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'author'         => 'nullable|string|max:255',
            'isbn'           => 'nullable|string|max:20',
            'description'    => 'nullable|string',
            'published_year' => 'nullable|string|max:20',
            'cover_url'      => 'nullable|url|max:2048',
        ]);

        $slug = Str::slug($validated['title']) . '-' . substr(uniqid(), -5);

        $product = Product::updateOrCreate(
            ['slug' => $slug],
            [
                'title'       => $validated['title'],
                'author'      => $validated['author'] ?? 'Unknown',
                'slug'        => $slug,
                'description' => $validated['description'] ?? null,
                'image_path'  => null,
                'rating'      => 0,
                'stock_quantity' => 0,
            ]
        );

        return redirect()->route('admin.books.edit', $product->id)
            ->with('success', "'{$product->title}' imported. Please review and complete the details.");
    }
}
