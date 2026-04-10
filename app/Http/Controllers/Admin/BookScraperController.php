<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Category;
use App\Models\Product;
use App\Services\BookScraperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BookScraperController extends Controller
{
    public function __construct(private readonly BookScraperService $scraper) {}

    public function index()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.scraper.index', compact('categories'));
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
            'handle'      => 'required|string|max:255',
            'title'       => 'required|string|max:255|unique:products,title',
            'author'      => 'nullable|string|max:255',
            'price'       => 'nullable|numeric|min:0',
            'cover_url'   => 'nullable|url|max:2048',
            'category_id' => 'nullable|integer|exists:categories,id',
        ]);

        $slug = Str::slug($validated['title']) . '-' . substr(uniqid(), -5);

        // Fetch description from the full product JSON
        $details = $this->scraper->fetchProductDetails($validated['handle']);

        // Use admin-chosen category; fall back to "Uncategorized" if none selected.
        if (!empty($validated['category_id'])) {
            $category = Category::findOrFail($validated['category_id']);
        } else {
            $category = Category::firstOrCreate(
                ['slug' => 'uncategorized'],
                ['name' => 'Uncategorized']
            );
        }

        // Download cover image to local storage
        $imagePath = null;
        if (!empty($validated['cover_url'])) {
            $imagePath = $this->scraper->downloadCover($validated['cover_url'], $slug);
        }

        $authorName = $validated['author'] ?? null;

        $product = Product::create([
            'title'           => $validated['title'],
            'author'          => $authorName ?? 'Unknown',
            'slug'            => $slug,
            'description'     => $details['description'],
            'synopsis'        => $details['synopsis'],
            'category_id'     => $category->id,
            'paperback_price' => $validated['price'] ?? 0,
            'hardcover_price' => null,
            'stock_quantity'  => 10000,
            'rating'          => 0,
            'image_path'      => $imagePath,
        ]);

        $product->categories()->sync([$category->id]);

        // Create or look up the Author record and attach it via the pivot.
        if ($authorName) {
            $author = Author::firstOrCreate(
                ['slug' => Str::slug($authorName)],
                ['name' => $authorName]
            );
            $product->authors()->sync([$author->id]);
        }

        Cache::forget('homepage_data_v4');

        return redirect()->route('admin.books.edit', $product->id)
            ->with('success', "'{$product->title}' imported. Please review the details and set the stock.");
    }
}
