<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
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
            'work_key'       => 'nullable|string|max:100',
            'subjects_json'  => 'nullable|string',
        ]);

        $slug = Str::slug($validated['title']) . '-' . substr(uniqid(), -5);

        // 1. Decode subjects passed from the search results
        $searchSubjects = json_decode($validated['subjects_json'] ?? '[]', true) ?? [];

        // 2. Fetch full work details from Open Library for richer description + subjects
        $workDetails = ['description' => null, 'subjects' => []];
        if (!empty($validated['work_key'])) {
            $workDetails = $this->scraper->fetchWorkDetails($validated['work_key']);
        }

        // 3. Build description and synopsis.
        //    Prefer the full work description; fall back to the search snippet.
        $fullText    = $workDetails['description'] ?? $validated['description'] ?? null;
        $description = $fullText;
        $synopsis    = null;

        if ($fullText && strlen($fullText) > 600) {
            // Split on a sentence boundary around the 400-char mark
            $split = strpos($fullText, '. ', 400);
            if ($split !== false) {
                $description = trim(substr($fullText, 0, $split + 1));
                $synopsis    = trim(substr($fullText, $split + 2));
            }
        }

        // 4. Resolve category — merge work subjects with the search result subjects
        $allSubjects = array_merge($workDetails['subjects'], $searchSubjects);
        $category    = $this->resolveCategory($allSubjects);

        // 5. Download cover image to local storage
        $imagePath = null;
        if (!empty($validated['cover_url'])) {
            $imagePath = $this->scraper->downloadCover($validated['cover_url'], $slug);
        }

        // 6. Create the product
        $product = Product::create([
            'title'           => $validated['title'],
            'author'          => $validated['author'] ?? 'Unknown',
            'slug'            => $slug,
            'description'     => $description,
            'synopsis'        => $synopsis,
            'category_id'     => $category->id,
            'paperback_price' => 0,
            'hardcover_price' => 0,
            'stock_quantity'  => 0,
            'rating'          => 0,
            'image_path'      => $imagePath,
        ]);

        return redirect()->route('admin.books.edit', $product->id)
            ->with('success', "'{$product->title}' imported successfully. Please set the price and review the details.");
    }

    /**
     * Find or create a Category from an array of Open Library subject strings.
     */
    private function resolveCategory(array $subjects): Category
    {
        foreach ($subjects as $subject) {
            $name = $this->normalizeSubject((string) $subject);
            if (!$name) {
                continue;
            }

            return Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }

        // Fallback: get or create a generic "Uncategorized" category
        return Category::firstOrCreate(
            ['slug' => 'uncategorized'],
            ['name' => 'Uncategorized']
        );
    }

    /**
     * Map a raw Open Library subject string to a clean category name.
     * Returns null for overly specific or noisy subjects we can't use.
     */
    private function normalizeSubject(string $subject): ?string
    {
        $map = [
            // Fiction genres
            'fiction'                           => 'Fiction',
            'general fiction'                   => 'Fiction',
            'literary fiction'                  => 'Fiction',
            'mystery'                           => 'Mystery',
            'mystery fiction'                   => 'Mystery',
            'detective and mystery stories'     => 'Mystery',
            'science fiction'                   => 'Science Fiction',
            'fantasy'                           => 'Fantasy',
            'fantasy fiction'                   => 'Fantasy',
            'epic fantasy'                      => 'Fantasy',
            'romance'                           => 'Romance',
            'romance fiction'                   => 'Romance',
            'love stories'                      => 'Romance',
            'horror'                            => 'Horror',
            'horror fiction'                    => 'Horror',
            'thriller'                          => 'Thriller',
            'suspense fiction'                  => 'Thriller',
            'historical fiction'                => 'Historical Fiction',
            'adventure fiction'                 => 'Adventure',
            'adventure stories'                 => 'Adventure',
            // Non-fiction
            'biography'                         => 'Biography',
            'autobiography'                     => 'Biography',
            'biography & autobiography'         => 'Biography',
            'history'                           => 'History',
            'world history'                     => 'History',
            'self-help'                         => 'Self-Help',
            'personal development'              => 'Self-Help',
            'philosophy'                        => 'Philosophy',
            'religion'                          => 'Religion',
            'spirituality'                      => 'Religion',
            'poetry'                            => 'Poetry',
            'drama'                             => 'Drama',
            'plays'                             => 'Drama',
            'travel'                            => 'Travel',
            'travel writing'                    => 'Travel',
            'cooking'                           => 'Cooking',
            'cookbooks'                         => 'Cooking',
            'art'                               => 'Art',
            'music'                             => 'Music',
            'sports'                            => 'Sports',
            'science'                           => 'Science',
            'technology'                        => 'Technology',
            'computers'                         => 'Technology',
            'business'                          => 'Business',
            'economics'                         => 'Economics',
            'politics'                          => 'Politics',
            'political science'                 => 'Politics',
            'psychology'                        => 'Psychology',
            'education'                         => 'Education',
            'health'                            => 'Health',
            'medicine'                          => 'Health',
            'comics'                            => 'Comics & Graphic Novels',
            'graphic novels'                    => 'Comics & Graphic Novels',
            // Children & YA
            'juvenile fiction'                  => 'Children',
            "children's stories"                => 'Children',
            "children's literature"             => 'Children',
            'picture books'                     => 'Children',
            'young adult fiction'               => 'Young Adult',
            'young adult literature'            => 'Young Adult',
            // Bangla / Bengali
            'bangla'                            => 'Bangla Literature',
            'bengali'                           => 'Bangla Literature',
            'bengali fiction'                   => 'Bangla Literature',
            'bengali literature'                => 'Bangla Literature',
            'bangla literature'                 => 'Bangla Literature',
            'bangladeshi literature'            => 'Bangla Literature',
            'বাংলা সাহিত্য'                    => 'Bangla Literature',
            'বাংলা'                             => 'Bangla Literature',
        ];

        $lower = mb_strtolower(trim($subject));

        if (isset($map[$lower])) {
            return $map[$lower];
        }

        // Accept short, clean subjects that don't look like identifiers
        if (mb_strlen($subject) <= 35 && !preg_match('/[\[\]()0-9@]/', $subject)) {
            return ucwords(mb_strtolower($subject));
        }

        return null;
    }
}
