<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class CatalogController extends Controller
{
    // 1. Load the Homepage
    public function home()
    {
        $topBooks = Product::orderBy('rating', 'desc')->take(5)->get();
        $popularAuthors = Product::select('author')->distinct()->inRandomOrder()->take(4)->get();
        
        // FIX 1: Fetch and pass the categories to the home view for the Quick Browse buttons
        $globalCategories = Category::all();
        
        return view('home', compact('topBooks', 'popularAuthors', 'globalCategories'));
    }

    // 2. Load Categories with Search & Filters
    public function categories(Request $request)
    {
        $query = Product::query();
        
        // NEW: Default title
        $pageTitle = 'All Books';
        $currentCategory = null; // We'll still keep this for the single-category logic

        // A. GLOBAL SEARCH
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('author', 'LIKE', "%{$searchTerm}%");
            });
            $pageTitle = 'Search Results';
        }

        // B. QUICK BROWSE LINKS (FROM HOMEPAGE)
        if ($request->filled('category')) {
            $currentCategory = Category::where('slug', $request->category)->first();
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
            
            if ($currentCategory) {
                $pageTitle = $currentCategory->name;
            }
        }

        // C. GENRE CHECKBOXES (FROM SIDEBAR) - THE FIX IS HERE
        if ($request->has('genres') && is_array($request->genres) && count($request->genres) > 0) {
            $query->whereIn('category_id', $request->genres);
            
            // If only ONE genre is ticked, grab its name for the title!
            if (count($request->genres) === 1) {
                $checkedGenre = Category::find($request->genres[0]);
                if ($checkedGenre) {
                    $pageTitle = $checkedGenre->name;
                }
            } else {
                // If multiple boxes are ticked
                $pageTitle = 'Multiple Genres';
            }
        }

        // D. AUTHOR CHECKBOXES
        if ($request->has('authors') && is_array($request->authors)) {
            $query->whereIn('author', $request->authors);
            // Optional: If they only filter by one author, change the title
            if (count($request->authors) === 1 && $pageTitle === 'All Books') {
                $pageTitle = 'Books by ' . $request->authors[0];
            }
        }

        // E. MINIMUM RATING
        if ($request->filled('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        // F. PRICE SLIDERS
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $products = $query->paginate(10)->withQueryString(); 
        $genres = Category::all();
        $authors = Product::select('author')->distinct()->whereNotNull('author')->where('author', '!=', '')->orderBy('author')->pluck('author');
        
        // Pass $pageTitle to the view instead of $currentCategory
        return view('categories.index', compact('products', 'genres', 'authors', 'pageTitle'));
    }
    public function authors()
    {
        // Get unique authors and count how many books they have
        $authors = Product::select('author')
            ->selectRaw('count(*) as book_count')
            ->whereNotNull('author')
            ->where('author', '!=', '')
            ->groupBy('author')
            ->orderBy('author')
            ->paginate(24);

        return view('authors.index', compact('authors'));
    }

    // 5. Bestsellers Page
    public function bestsellers(Request $request)
    {
        // Fetch books with a rating of 4 or higher
        $products = Product::where('rating', '>=', 4)
                           ->orderBy('rating', 'desc')
                           ->paginate(12);

        // We need these for the sidebar filters
        $genres = Category::all();
        $authors = Product::select('author')->distinct()->whereNotNull('author')->where('author', '!=', '')->orderBy('author')->pluck('author');
        
        // Force the page title
        $pageTitle = 'Bestsellers & Top Rated';

        // Reuse the categories view!
        return view('categories.index', compact('products', 'genres', 'authors', 'pageTitle'));
    }
    // 3. Load Single Product Page
    public function show($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        
        $relatedProducts = Product::where('id', '!=', $product->id)
                                  ->inRandomOrder()
                                  ->take(3)
                                  ->get();
                                         
        return view('products.show', compact('product', 'relatedProducts'));
    }
}