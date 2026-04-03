<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\HeroSlide;
use App\Models\Quote;
use Illuminate\Support\Facades\Cache; // NEW: Added for caching

class CatalogController extends Controller
{
    // 1. Load the Homepage (FIX 2.1: Cached for 5 minutes to prevent N+1 and slow loads)
    // ADD THIS TO THE BOTTOM OF CatalogController
    public function liveSearch(Request $request)
    {
        $request->validate(['q' => 'nullable|string|max:100']);

        $term = $request->query('q');

        if (!$term || strlen($term) < 2) {
            return response()->json([]);
        }

        // Split the search term into individual words
        $words = explode(' ', $term);
        $query = Product::query();

        foreach ($words as $word) {
            // Create a fuzzy string for typos (e.g., "poter" becomes "%p%o%t%e%r%")
            $fuzzyWord = '%' . implode('%', str_split($word)) . '%';

            $query->where(function ($q) use ($word, $fuzzyWord) {
                $q->where('title', 'LIKE', "%{$word}%")
                  ->orWhere('title', 'LIKE', $fuzzyWord) // Typo tolerance
                  ->orWhere('author', 'LIKE', "%{$word}%")
                  ->orWhere('description', 'LIKE', "%{$word}%")
                  ->orWhere('synopsis', 'LIKE', "%{$word}%")
                  ->orWhereHas('category', function ($cq) use ($word) {
                      $cq->where('name', 'LIKE', "%{$word}%");
                  });
            });
        }

        // Only grab the columns we actually need for the dropdown to keep it blazing fast
        $results = $query->select('id', 'title', 'author', 'slug', 'image_path')
                         ->take(5) // Limit to 5 results so the dropdown isn't massive
                         ->get();

        return response()->json($results);
    }

    public function home()
    {
        $data = Cache::remember('homepage_data', 300, function () {
            return [
                'topBooks' => Product::orderBy('rating', 'desc')->take(5)->get(),
                'popularAuthors' => Product::selectRaw('author, count(*) as book_count')
                                ->whereNotNull('author')
                                ->where('author', '!=', '')
                                ->groupBy('author')
                                ->orderBy('book_count', 'desc')
                                ->take(4)
                                ->get(),
                'globalCategories' => Category::all(),
                'quote' => Quote::inRandomOrder()->first(),
                'heroSlides' => HeroSlide::orderBy('order', 'asc')->get()
            ];
        });

        return view('home', $data);
    }

    // 2. Load Categories with Search & Filters
    public function categories(Request $request)
    {
        $query = Product::query();

        $pageTitle = 'All Books';
        $currentCategory = null;

        // A. GLOBAL SEARCH (FIX 2.5 & D-001: Upgraded Scope & Fuzzy Match)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $words = explode(' ', $searchTerm);

            foreach ($words as $word) {
                $fuzzyWord = '%' . implode('%', str_split($word)) . '%';

                $query->where(function ($q) use ($word, $fuzzyWord) {
                    $q->where('title', 'LIKE', "%{$word}%")
                      ->orWhere('title', 'LIKE', $fuzzyWord)
                      ->orWhere('author', 'LIKE', "%{$word}%")
                      ->orWhere('description', 'LIKE', "%{$word}%")
                      ->orWhere('synopsis', 'LIKE', "%{$word}%")
                      ->orWhereHas('category', function ($cq) use ($word) {
                          $cq->where('name', 'LIKE', "%{$word}%");
                      });
                });
            }
            $pageTitle = 'Search Results';
        }

        // B. QUICK BROWSE LINKS (FROM HOMEPAGE)
        if ($request->filled('category')) {
            $currentCategory = Category::where('slug', $request->category)->first();
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });

            if ($currentCategory) {
                $pageTitle = $currentCategory->name;
            }
        }

        // C. GENRE CHECKBOXES (FROM SIDEBAR)
        if ($request->has('genres') && is_array($request->genres) && count($request->genres) > 0) {
            $query->whereIn('category_id', $request->genres);

            if (count($request->genres) === 1) {
                $checkedGenre = Category::find($request->genres[0]);
                if ($checkedGenre) {
                    $pageTitle = $checkedGenre->name;
                }
            } else {
                $pageTitle = 'Multiple Genres';
            }
        }

        // D. AUTHOR CHECKBOXES
        if ($request->has('authors') && is_array($request->authors)) {
            $query->whereIn('author', $request->authors);
            if (count($request->authors) === 1 && $pageTitle === 'All Books') {
                $pageTitle = 'Books by ' . $request->authors[0];
            }
        }

        // E. MINIMUM RATING
        if ($request->filled('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        // F. PRICE SLIDERS - Filter by minimum of paperback/hardcover prices
        if ($request->filled('min_price')) {
            $query->where(function ($q) use ($request) {
                $q->where(function ($sq) use ($request) {
                    // Paperback meets min price
                    $sq->whereNotNull('paperback_price')
                       ->where('paperback_price', '>=', $request->min_price);
                })->orWhere(function ($sq) use ($request) {
                    // Hardcover meets min price (and no paperback)
                    $sq->whereNull('paperback_price')
                       ->whereNotNull('hardcover_price')
                       ->where('hardcover_price', '>=', $request->min_price);
                });
            });
        }
        if ($request->filled('max_price')) {
            $query->where(function ($q) use ($request) {
                $q->where(function ($sq) use ($request) {
                    // Paperback meets max price
                    $sq->whereNotNull('paperback_price')
                       ->where('paperback_price', '<=', $request->max_price);
                })->orWhere(function ($sq) use ($request) {
                    // Hardcover meets max price (and no paperback)
                    $sq->whereNull('paperback_price')
                       ->whereNotNull('hardcover_price')
                       ->where('hardcover_price', '<=', $request->max_price);
                });
            });
        }

        // FIX 2.6: Changed ->get() to ->paginate(24) to prevent memory crashes on large catalogs
        // Added withQueryString() so pagination works WITH the applied filters
        $products = $query->paginate(24)->withQueryString();

        $genres = Category::all();

        // FIX 3.4: Optimized Author fetch by grouping instead of distinct pluck
        $authors = Product::selectRaw('author, MIN(id) as min_id')
            ->whereNotNull('author')
            ->where('author', '!=', '')
            ->groupBy('author')
            ->orderBy('author')
            ->pluck('author');

        return view('categories.index', compact('products', 'genres', 'authors', 'pageTitle'));
    }

    public function authors(Request $request)
    {
        $authors = Product::select('products.author')
            ->selectRaw('count(*) as book_count')
            ->selectRaw('MAX(authors.photo_path) as photo_path')
            ->leftJoin('authors', 'products.author', '=', 'authors.name')
            ->whereNotNull('products.author')
            ->where('products.author', '!=', '')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('products.author', 'LIKE', '%' . $request->search . '%');
            })
            ->groupBy('products.author')
            ->orderBy('products.author')
            ->paginate(24)
            ->withQueryString();

        return view('authors.index', compact('authors'));
    }

    // 5. Bestsellers Page
    public function bestsellers(Request $request)
    {
        // Added withQueryString() here too just in case filters are ever added
        $products = Product::where('rating', '>=', 4)
                           ->orderBy('rating', 'desc')
                           ->paginate(12)
                           ->withQueryString();

        $genres = Category::all();

        // FIX 3.4 (Applied here as well)
        $authors = Product::selectRaw('author, MIN(id) as min_id')
            ->whereNotNull('author')
            ->where('author', '!=', '')
            ->groupBy('author')
            ->orderBy('author')
            ->pluck('author');

        $pageTitle = 'Bestsellers & Top Rated';

        return view('categories.index', compact('products', 'genres', 'authors', 'pageTitle'));
    }

    // 3. Load Single Product Page
    public function show($slug)
    {
        $product = Product::with('category')->where('slug', $slug)->firstOrFail();

        $relatedProducts = Product::where('id', '!=', $product->id)
                                  ->inRandomOrder()
                                  ->take(3)
                                  ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
