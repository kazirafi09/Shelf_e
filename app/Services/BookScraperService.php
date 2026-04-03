<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BookScraperService
{
    private const SEARCH_URL = 'https://openlibrary.org/search.json';
    private const BOOK_URL   = 'https://openlibrary.org/api/books';
    private const COVER_URL  = 'https://covers.openlibrary.org/b/isbn/%s-M.jpg';

    public function searchByTitle(string $title): array
    {
        $response = Http::get(self::SEARCH_URL, [
            'title'  => $title,
            'limit'  => 10,
            'fields' => 'title,author_name,isbn,first_publish_year,first_sentence',
        ]);

        if ($response->failed()) {
            throw new \Exception("Open Library search failed with status {$response->status()}.");
        }

        $docs = $response->json('docs', []);

        return array_map(function (array $doc) {
            $isbn = $doc['isbn'][0] ?? null;

            return [
                'title'          => $doc['title'] ?? null,
                'author'         => $doc['author_name'][0] ?? null,
                'isbn'           => $isbn,
                'cover_url'      => $isbn ? sprintf(self::COVER_URL, $isbn) : null,
                'description'    => $doc['first_sentence'][0] ?? null,
                'published_year' => $doc['first_publish_year'] ?? null,
            ];
        }, $docs);
    }

    public function fetchByISBN(string $isbn): ?array
    {
        $key = "ISBN:{$isbn}";

        $response = Http::get(self::BOOK_URL, [
            'bibkeys' => $key,
            'format'  => 'json',
            'jscmd'   => 'data',
        ]);

        if ($response->failed()) {
            throw new \Exception("Open Library ISBN lookup failed with status {$response->status()}.");
        }

        $data = $response->json($key);

        if (empty($data)) {
            return null;
        }

        $author = $data['authors'][0]['name'] ?? null;
        $description = is_array($data['description'] ?? null)
            ? ($data['description']['value'] ?? null)
            : ($data['description'] ?? null);

        return [
            'title'          => $data['title'] ?? null,
            'author'         => $author,
            'isbn'           => $isbn,
            'cover_url'      => $data['cover']['medium'] ?? sprintf(self::COVER_URL, $isbn),
            'description'    => $description,
            'published_year' => $data['publish_date'] ?? null,
        ];
    }
}
