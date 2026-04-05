<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class BookScraperService
{
    private const BASE_URL = 'https://booktankbd.com';

    /**
     * Search Book Tank BD using Shopify's predictive search API.
     */
    public function searchByTitle(string $query): array
    {
        $response = Http::timeout(20)->get(self::BASE_URL . '/search/suggest.json', [
            'q'                => $query,
            'resources[type]'  => 'product',
            'resources[limit]' => 10,
        ]);

        if ($response->failed()) {
            throw new \Exception("Book Tank BD search failed with status {$response->status()}.");
        }

        $products = $response->json('resources.results.products', []);

        return array_map(fn(array $p) => $this->normalizeSearchResult($p), $products);
    }

    /**
     * Fetch full product details (description, tags) from the product JSON endpoint.
     */
    public function fetchProductDetails(string $handle): array
    {
        $response = Http::timeout(20)->get(self::BASE_URL . '/products/' . $handle . '.json');

        if ($response->failed()) {
            return ['description' => null, 'synopsis' => null, 'tags' => [], 'product_type' => null, 'genre' => null];
        }

        $product  = $response->json('product', []);
        $bodyHtml = $product['body_html'] ?? '';

        $rawText = trim(strip_tags(html_entity_decode($bodyHtml)));

        // Strip structured metadata lines (e.g. "Genre: Fiction", "Author: X") from the body
        // and capture the genre value so the controller can use it for category resolution.
        [$cleanText, $genre] = $this->extractMetadata($rawText);

        $description = $cleanText ?: null;
        $synopsis    = null;

        if ($cleanText && mb_strlen($cleanText) > 600) {
            $split = strpos($cleanText, '. ', 400);
            if ($split !== false) {
                $description = trim(substr($cleanText, 0, $split + 1));
                $synopsis    = trim(substr($cleanText, $split + 2));
            }
        }

        return [
            'description'  => $description,
            'synopsis'     => $synopsis,
            'tags'         => (array) ($product['tags'] ?? []),
            'product_type' => $product['product_type'] ?? null,
            'genre'        => $genre,
        ];
    }

    /**
     * Strip metadata / promotional lines from scraped body text and extract genre.
     *
     * Handles lines like:
     *   "📌 Genre: Self-help Book."   → captures genre, drops line
     *   "➡️ Best Printing quality."   → dropped (emoji bullet)
     *   "✅ Matt cover (Paperback)."  → dropped (emoji bullet)
     *   "📚 সেলাই করা বাইন্ডিং"       → dropped (emoji bullet)
     *
     * Returns [cleanText, genre].
     */
    private function extractMetadata(string $text): array
    {
        $genre = null;
        $lines = preg_split('/\r?\n/', $text);
        $kept  = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            // Match "Genre/Category/Subject: X" possibly preceded by 1-3 emoji chars.
            // Uses .{0,3} with /u so it counts Unicode characters, not bytes.
            if (preg_match('/^.{0,3}(Genre|Category|Subject)\s*:\s*(.+)$/iu', $line, $m) && $genre === null) {
                $raw   = trim($m[2], " \t.,;");
                // Strip trailing "book" / "books" suffix ("Self-help Book" → "Self-help")
                $genre = trim(preg_replace('/\s+books?$/i', '', $raw));
                continue; // drop from body
            }

            // Drop other metadata key: value lines (with optional emoji prefix)
            if (preg_match('/^.{0,3}(Author|Publisher|Edition|Pages?|ISBN|Language)\s*:\s*/iu', $line)) {
                continue;
            }

            // Drop any line whose first character is an emoji or symbol
            // (Unicode >= U+2600: Misc Symbols, Dingbats, Emoji blocks).
            // Bengali/Latin/Arabic/etc. are all below U+0600-U+09FF and safe.
            if (mb_ord(mb_substr($line, 0, 1)) >= 0x2600) {
                continue;
            }

            $kept[] = $line;
        }

        return [trim(implode("\n", $kept)), $genre];
    }

    /**
     * Download a remote cover image and store it locally.
     * Returns the relative storage path or null on failure.
     */
    public function downloadCover(string $url, string $slug): ?string
    {
        try {
            $response = Http::timeout(20)->get($url);

            if ($response->failed()) {
                return null;
            }

            $body = $response->body();

            if (strlen($body) < 1000) {
                return null;
            }

            $contentType = $response->header('Content-Type') ?? '';

            if (!str_starts_with($contentType, 'image/')) {
                return null;
            }

            $ext = match (true) {
                str_contains($contentType, 'png')  => 'png',
                str_contains($contentType, 'webp') => 'webp',
                default                            => 'jpg',
            };

            $filename = 'books/' . $slug . '-' . substr(uniqid(), -6) . '.' . $ext;
            Storage::disk('public')->put($filename, $body);

            return $filename;
        } catch (\Exception) {
            return null;
        }
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private function normalizeSearchResult(array $p): array
    {
        $rawTitle = $p['title'] ?? '';
        [$title, $author] = $this->splitTitleAuthor($rawTitle);

        // Shopify suggest uses 'image.url'; product JSON uses 'image.src'
        $coverUrl = $p['image']['url'] ?? $p['image']['src'] ?? null;

        // Extract handle from URL path: /products/{handle}
        $handle = '';
        if (preg_match('#/products/([^/?]+)#', $p['url'] ?? '', $m)) {
            $handle = $m[1];
        }

        return [
            'handle'    => $handle,
            'title'     => $title,
            'author'    => $author,
            'price'     => $p['price'] ?? null,
            'cover_url' => $coverUrl,
        ];
    }

    /**
     * Split "Book Title by Author Name" into [$title, $author].
     * Uses a greedy match so the LAST " by " is the separator.
     */
    private function splitTitleAuthor(string $raw): array
    {
        if (preg_match('/^(.*\S)\s+by\s+(\S.+)$/i', trim($raw), $m)) {
            return [trim($m[1]), trim($m[2])];
        }

        return [trim($raw), null];
    }
}
