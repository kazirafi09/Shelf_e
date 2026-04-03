<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class BookScraperService
{
    private const SEARCH_URL     = 'https://openlibrary.org/search.json';
    private const WORKS_BASE     = 'https://openlibrary.org';
    private const COVER_ID_URL   = 'https://covers.openlibrary.org/b/id/%s-L.jpg';
    private const COVER_ISBN_URL = 'https://covers.openlibrary.org/b/isbn/%s-L.jpg';

    /**
     * Search Open Library using a generic `q` parameter.
     * This supports any language including Bangla script and romanised Bangla.
     */
    public function searchByTitle(string $query): array
    {
        $response = Http::timeout(20)->get(self::SEARCH_URL, [
            'q'      => $query,
            'limit'  => 10,
            'fields' => 'key,title,author_name,isbn,first_publish_year,subject,first_sentence,cover_i,language',
        ]);

        if ($response->failed()) {
            throw new \Exception("Open Library search failed with status {$response->status()}.");
        }

        $docs = $response->json('docs', []);

        return array_map(function (array $doc) {
            $isbn    = $doc['isbn'][0] ?? null;
            $coverId = $doc['cover_i'] ?? null;

            // Prefer cover ID (reliable) over ISBN-based URL (often broken)
            if ($coverId) {
                $coverUrl = sprintf(self::COVER_ID_URL, $coverId);
            } elseif ($isbn) {
                $coverUrl = sprintf(self::COVER_ISBN_URL, $isbn);
            } else {
                $coverUrl = null;
            }

            // first_sentence can be a string or {"value": "..."} object
            $description = null;
            if (!empty($doc['first_sentence'])) {
                $fs = $doc['first_sentence'];
                $description = is_array($fs) ? ($fs['value'] ?? $fs[0] ?? null) : $fs;
            }

            return [
                'work_key'       => $doc['key'] ?? null,     // e.g. /works/OL12345W
                'title'          => $doc['title'] ?? null,
                'author'         => $doc['author_name'][0] ?? null,
                'isbn'           => $isbn,
                'cover_url'      => $coverUrl,
                'description'    => $description,
                'published_year' => $doc['first_publish_year'] ?? null,
                'subjects'       => array_slice($doc['subject'] ?? [], 0, 8),
                'language'       => $doc['language'][0] ?? null,
            ];
        }, $docs);
    }

    /**
     * Fetch the full Open Library Works record for a given work key.
     * Returns ['description' => string|null, 'subjects' => array].
     */
    public function fetchWorkDetails(string $workKey): array
    {
        $response = Http::timeout(15)->get(self::WORKS_BASE . $workKey . '.json');

        if ($response->failed()) {
            return ['description' => null, 'subjects' => []];
        }

        $data = $response->json();

        $description = null;
        if (!empty($data['description'])) {
            $desc = $data['description'];
            $description = is_array($desc) ? ($desc['value'] ?? null) : $desc;
        }

        return [
            'description' => $description,
            'subjects'    => $data['subjects'] ?? [],
        ];
    }

    /**
     * Download a remote cover image, reject tiny placeholder GIFs,
     * store it under storage/app/public/books/ and return the relative path.
     * Returns null if the URL is missing, download fails, or the image is a placeholder.
     */
    public function downloadCover(string $url, string $slug): ?string
    {
        try {
            $response = Http::timeout(20)->get($url);

            if ($response->failed()) {
                return null;
            }

            $body = $response->body();

            // Open Library returns a 1×1 GIF for books with no cover.
            // These are always under ~200 bytes — reject them.
            if (strlen($body) < 1000) {
                return null;
            }

            $contentType = $response->header('Content-Type') ?? '';

            // Only accept real images
            if (!str_starts_with($contentType, 'image/')) {
                return null;
            }

            $ext = match(true) {
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
}
