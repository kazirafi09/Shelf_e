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

        // Fetch description + tags from the full product JSON
        $details = $this->scraper->fetchProductDetails($validated['handle']);

        // Use admin-chosen category if provided; otherwise auto-resolve.
        // Priority: 1) admin pick, 2) genre extracted from body text, 3) Shopify tags/product_type.
        // Genres may be comma/slash-separated (e.g. "Self help, Business") — split into multiple.
        if (!empty($validated['category_id'])) {
            $resolvedCategories = [Category::findOrFail($validated['category_id'])];
        } elseif (!empty($details['genre'])) {
            $rawSubjects = preg_split('/\s*[,\/]\s*/', $details['genre']);
            $resolvedCategories = $this->resolveCategories($rawSubjects);
        } else {
            $rawSubjects = array_values(array_filter(array_merge(
                $details['tags'],
                $details['product_type'] ? [$details['product_type']] : []
            )));
            // Also split any comma-joined tag strings
            $rawSubjects = array_merge(...array_map(fn($s) => preg_split('/\s*[,\/]\s*/', $s), $rawSubjects));
            $resolvedCategories = $this->resolveCategories($rawSubjects);
        }

        $category = $resolvedCategories[0]; // first resolved = primary category_id

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

        // Attach all resolved categories to the pivot table.
        $product->categories()->sync(collect($resolvedCategories)->pluck('id')->all());

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

    // ── Private helpers ──────────────────────────────────────────────────────

    /**
     * Resolve an array of raw subject strings into Category models.
     * Each subject is normalized; unrecognized subjects are skipped.
     * Returns at least one Category (falls back to Uncategorized).
     */
    private function resolveCategories(array $subjects): array
    {
        $categories = [];

        foreach ($subjects as $subject) {
            $name = $this->normalizeSubject((string) $subject);
            if (!$name) {
                continue;
            }

            $categories[] = Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }

        if (empty($categories)) {
            $categories[] = Category::firstOrCreate(
                ['slug' => 'uncategorized'],
                ['name' => 'Uncategorized']
            );
        }

        return $categories;
    }

    private function normalizeSubject(string $subject): ?string
    {
        $map = [
            'fiction'                       => 'Fiction',
            'general fiction'               => 'Fiction',
            'literary fiction'              => 'Fiction',
            'mystery'                       => 'Mystery',
            'mystery fiction'               => 'Mystery',
            'science fiction'               => 'Science Fiction',
            'fantasy'                       => 'Fantasy',
            'fantasy fiction'               => 'Fantasy',
            'romance'                       => 'Romance',
            'horror'                        => 'Horror',
            'thriller'                      => 'Thriller',
            'historical fiction'            => 'Historical Fiction',
            'adventure fiction'             => 'Adventure',
            'adventure stories'             => 'Adventure',
            'biography'                     => 'Biography',
            'autobiography'                 => 'Biography',
            'biography & autobiography'     => 'Biography',
            'history'                       => 'History',
            'self-help'                     => 'Self-Help',
            'personal development'          => 'Self-Help',
            'philosophy'                    => 'Philosophy',
            'religion'                      => 'Religion',
            'spirituality'                  => 'Religion',
            'poetry'                        => 'Poetry',
            'drama'                         => 'Drama',
            'travel'                        => 'Travel',
            'cooking'                       => 'Cooking',
            'art'                           => 'Art',
            'music'                         => 'Music',
            'sports'                        => 'Sports',
            'science'                       => 'Science',
            'technology'                    => 'Technology',
            'business'                      => 'Business',
            'finance'                       => 'Finance',
            'economics'                     => 'Economics',
            'politics'                      => 'Politics',
            'psychology'                    => 'Psychology',
            'education'                     => 'Education',
            'health'                        => 'Health',
            'comics'                        => 'Comics & Graphic Novels',
            'graphic novels'                => 'Comics & Graphic Novels',
            'juvenile fiction'              => 'Children',
            "children's stories"            => 'Children',
            'young adult fiction'           => 'Young Adult',
            'bangla'                        => 'Bangla Literature',
            'bengali'                       => 'Bangla Literature',
            'bengali fiction'               => 'Bangla Literature',
            'bengali literature'            => 'Bangla Literature',
            'bangla literature'             => 'Bangla Literature',
            'bangladeshi literature'        => 'Bangla Literature',
            'বাংলা সাহিত্য'                => 'Bangla Literature',
            'বাংলা'                         => 'Bangla Literature',
        ];

        // Strip trailing genre-qualifier suffixes so "Self-help Book" → "Self-help",
        // "Business Book" → "Business", etc., before hitting the map.
        $subject = preg_replace('/\s+books?\s*$/i', '', trim($subject));

        $lower = mb_strtolower(trim($subject));

        if (isset($map[$lower])) {
            return $map[$lower];
        }

        // Only accept multi-word subjects that look like genre/topic phrases,
        // not single proper nouns (which are likely author names from Shopify tags).
        if (
            mb_strlen($subject) <= 35
            && !preg_match('/[\[\]()0-9@]/', $subject)
            && str_word_count($subject) >= 2
        ) {
            return ucwords(mb_strtolower($subject));
        }

        return null;
    }
}
