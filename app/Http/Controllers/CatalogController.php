<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HeroSlide;
use App\Models\Product;
use App\Models\Category;
use App\Models\Author;
use App\Models\Quote;
use Illuminate\Support\Facades\Cache;

class CatalogController extends Controller
{
    /**
     * Escape LIKE wildcards (% and _) from user input so the pattern behaves
     * like a plain substring match rather than a wildcard expression.
     */
    private function escapeLike(string $value): string
    {
        return addcslashes($value, '%_\\');
    }

    /**
     * Split the raw user query into searchable tokens (lowercased, trimmed,
     * min 2 chars) plus a normalized full-term version for phrase boosts.
     *
     * @return array{term: string, words: array<int, string>}
     */
    private function tokenizeSearch(string $raw): array
    {
        $term = mb_strtolower(trim($raw));
        $words = array_values(array_filter(
            preg_split('/\s+/u', $term) ?: [],
            fn ($w) => mb_strlen($w) >= 2
        ));

        if (empty($words) && $term !== '') {
            $words = [$term];
        }

        return ['term' => $term, 'words' => $words];
    }

    /**
     * Build a relevance scoring SQL fragment plus its bindings for a search.
     * Heavier weights on title/author so on-topic books bubble to the top.
     *
     * @param  array<int, string>  $words
     * @return array{sql: string, bindings: array<int, string>}
     */
    private function buildRelevanceScore(string $term, array $words): array
    {
        $escTerm = $this->escapeLike($term);
        $parts = [];
        $bindings = [];

        // Whole-phrase boosts
        $parts[] = '(CASE WHEN LOWER(title) = ? THEN 100 ELSE 0 END)';
        $bindings[] = $term;
        $parts[] = '(CASE WHEN LOWER(title) LIKE ? THEN 60 ELSE 0 END)';
        $bindings[] = $escTerm . '%';
        $parts[] = '(CASE WHEN LOWER(title) LIKE ? THEN 35 ELSE 0 END)';
        $bindings[] = '%' . $escTerm . '%';
        $parts[] = '(CASE WHEN LOWER(author) = ? THEN 45 ELSE 0 END)';
        $bindings[] = $term;
        $parts[] = '(CASE WHEN LOWER(author) LIKE ? THEN 25 ELSE 0 END)';
        $bindings[] = '%' . $escTerm . '%';

        // Per-word boosts (so multi-word queries still rank well)
        foreach ($words as $word) {
            $escWord = $this->escapeLike($word);
            $parts[] = '(CASE WHEN LOWER(title) LIKE ? THEN 8 ELSE 0 END)';
            $bindings[] = '%' . $escWord . '%';
            $parts[] = '(CASE WHEN LOWER(author) LIKE ? THEN 5 ELSE 0 END)';
            $bindings[] = '%' . $escWord . '%';
        }

        return ['sql' => implode(' + ', $parts), 'bindings' => $bindings];
    }

    // Live autocomplete endpoint for the navbar dropdown.
    public function liveSearch(Request $request)
    {
        $request->validate(['q' => 'nullable|string|max:100']);

        ['term' => $term, 'words' => $words] = $this->tokenizeSearch((string) $request->query('q', ''));

        if ($term === '' || mb_strlen($term) < 2 || empty($words)) {
            return response()->json([]);
        }

        $query = Product::query();

        // Every token must match title, author, or category (AND across words).
        // Description/synopsis intentionally excluded here to keep the dropdown
        // focused on title/author matches users expect from autocomplete.
        foreach ($words as $word) {
            $like = '%' . $this->escapeLike($word) . '%';
            $query->where(function ($q) use ($like) {
                $q->where('title', 'LIKE', $like)
                  ->orWhere('author', 'LIKE', $like)
                  ->orWhereHas('category', fn ($cq) => $cq->where('name', 'LIKE', $like));
            });
        }

        $score = $this->buildRelevanceScore($term, $words);

        $results = $query
            ->select('id', 'title', 'author', 'slug', 'image_path')
            ->selectRaw($score['sql'] . ' AS relevance', $score['bindings'])
            ->orderByDesc('relevance')
            ->orderBy('title')
            ->limit(8)
            ->get();

        return response()->json($results);
    }

    public function home()
    {
        $data = Cache::remember('homepage_data_v4', 3600, function () {
            return [
                'topBooks' => Product::withAvg('approvedReviews', 'rating')
                                ->withCount('approvedReviews')
                                ->orderBy('rating', 'desc')
                                ->take(5)
                                ->get(),
                'bestSellers' => Product::withAvg('approvedReviews', 'rating')
                                ->withCount('approvedReviews')
                                ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                                ->select('products.*')
                                ->selectRaw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
                                ->groupBy('products.id')
                                ->orderByDesc('total_sold')
                                ->take(5)
                                ->get(),
                'popularAuthors' => Product::select('products.author')
                                ->selectRaw('count(*) as book_count')
                                ->selectRaw('MAX(authors.photo_path) as photo_path')
                                ->leftJoin('authors', 'products.author', '=', 'authors.name')
                                ->whereNotNull('products.author')
                                ->where('products.author', '!=', '')
                                ->groupBy('products.author')
                                ->orderBy('book_count', 'desc')
                                ->take(4)
                                ->get(),
                'globalCategories' => Category::all(),
                'quote' => Quote::inRandomOrder()->first(),
            ];
        });

        // Deals are time-sensitive: short 60-second cache so the displayed
        // countdown stays accurate and expired deals fall off quickly.
        $data['dealsOfWeek'] = Cache::remember('homepage_deals_v1', 60, function () {
            return Product::withAvg('approvedReviews', 'rating')
                ->whereNotNull('sale_price')
                ->where('sale_ends_at', '>', now())
                ->orderBy('sale_ends_at', 'asc')   // soonest-expiring first
                ->take(6)
                ->get();
        });

        // Hero slides are not in the long cache — changes should show within 5 min
        $data['heroBooks'] = Cache::remember('hero_slides_v1', 300, function () {
            return HeroSlide::with('product')->orderBy('order')->orderBy('id')->get();
        });

        return view('home', $data);
    }

    // 2. Load Categories with Search & Filters
    public function categories(Request $request)
    {
        $query = Product::withAvg('approvedReviews', 'rating')->withCount('approvedReviews');

        $pageTitle = 'All Books';
        $currentCategory = null;
        $searchActive = false;

        // A. GLOBAL SEARCH — relevance-ranked, tokenized, wildcard-escaped.
        if ($request->filled('search')) {
            ['term' => $searchTerm, 'words' => $searchWords] = $this->tokenizeSearch((string) $request->search);

            if ($searchTerm !== '' && ! empty($searchWords)) {
                $searchActive = true;

                foreach ($searchWords as $word) {
                    $like = '%' . $this->escapeLike($word) . '%';
                    $query->where(function ($q) use ($like) {
                        $q->where('title', 'LIKE', $like)
                          ->orWhere('author', 'LIKE', $like)
                          ->orWhere('description', 'LIKE', $like)
                          ->orWhere('synopsis', 'LIKE', $like)
                          ->orWhereHas('category', function ($cq) use ($like) {
                              $cq->where('name', 'LIKE', $like);
                          });
                    });
                }

                $score = $this->buildRelevanceScore($searchTerm, $searchWords);
                $query->selectRaw('products.*')
                      ->selectRaw('(' . $score['sql'] . ') AS relevance', $score['bindings']);
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
            $query->whereHas('categories', function ($q) use ($request) {
                $q->whereIn('categories.id', $request->genres);
            });

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

        // G. ON SALE FILTER
        if ($request->boolean('on_sale')) {
            $query->whereNotNull('sale_price')
                  ->where(function ($q) {
                      $q->whereNull('sale_ends_at')->orWhere('sale_ends_at', '>', now());
                  });
            if ($pageTitle === 'All Books') {
                $pageTitle = 'Sale Items';
            }
        }

        // H. SORT
        if ($request->input('sort') === 'newest') {
            $query->orderByDesc('created_at');
            if ($pageTitle === 'All Books') {
                $pageTitle = 'New Arrivals';
            }
        } elseif ($searchActive) {
            // Default sort for search results is relevance (highest match score first),
            // then most recent so ties break toward fresh stock.
            $query->orderByDesc('relevance')->orderByDesc('created_at');
        }

        // I. NEW ARRIVALS — books added in the last 7 days
        if ($request->boolean('new_arrivals')) {
            $query->where('created_at', '>=', now()->subWeek())
                  ->orderByDesc('created_at');
            $pageTitle = 'New Arrivals';
        }

        // J. IN DEMAND — books that have at least one order
        if ($request->boolean('in_demand')) {
            $query->whereHas('orderItems');
            $pageTitle = 'In Demand';
        }

        $products = $query->paginate(30)->withQueryString();

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
            ->selectRaw('count(distinct products.id) as book_count')
            ->selectRaw('MAX(authors.photo_path) as photo_path')
            ->leftJoin('authors', 'products.author', '=', 'authors.name')
            ->whereNotNull('products.author')
            ->where('products.author', '!=', '')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('products.author', 'LIKE', '%' . $request->search . '%');
            })
            ->groupBy('products.author')
            ->orderBy('products.author')
            ->paginate(30)
            ->withQueryString();

        return view('authors.index', compact('authors'));
    }

    // 5. Bestsellers Page — sorted by total units sold, highest to lowest
    public function bestsellers()
    {
        $products = Product::withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->select('products.*')
            ->selectRaw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->paginate(30)
            ->withQueryString();

        $genres = Category::all();

        $authors = Product::selectRaw('author, MIN(id) as min_id')
            ->whereNotNull('author')
            ->where('author', '!=', '')
            ->groupBy('author')
            ->orderBy('author')
            ->pluck('author');

        $pageTitle    = 'Bestsellers';
        $isBestsellers = true;

        return view('categories.index', compact('products', 'genres', 'authors', 'pageTitle', 'isBestsellers'));
    }

    public function series()
    {
        $products = Product::withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->whereHas('category', function ($q) {
                $q->where('name', 'LIKE', '%series%');
            })
            ->paginate(30)
            ->withQueryString();

        $genres = Category::all();

        $authors = Product::selectRaw('author, MIN(id) as min_id')
            ->whereNotNull('author')
            ->where('author', '!=', '')
            ->groupBy('author')
            ->orderBy('author')
            ->pluck('author');

        $pageTitle = 'Series';

        return view('categories.index', compact('products', 'genres', 'authors', 'pageTitle'));
    }

    // 3. Load Single Product Page
    public function show($slug)
    {
        $product = Product::with(['category', 'previews', 'approvedReviews.user'])
            ->withCount('approvedReviews')
            ->withAvg('approvedReviews', 'rating')
            ->where('slug', $slug)
            ->firstOrFail();

        $authorModel = $product->author
            ? Author::where('name', $product->author)->first()
            : null;

        $relatedProducts = Product::where('id', '!=', $product->id)
                                  ->inRandomOrder()
                                  ->take(3)
                                  ->get();

        return view('products.show', compact('product', 'relatedProducts', 'authorModel'));
    }
}
