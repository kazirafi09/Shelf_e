<?php

namespace Tests\Unit\Services;

use App\Services\BookScraperService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BookScraperServiceTest extends TestCase
{
    use RefreshDatabase;

    private BookScraperService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BookScraperService();
    }

    public function test_search_by_title_returns_normalized_array(): void
    {
        Http::fake([
            'openlibrary.org/search.json*' => Http::response([
                'docs' => [
                    [
                        'title'             => 'The Great Gatsby',
                        'author_name'       => ['F. Scott Fitzgerald'],
                        'isbn'              => ['9780743273565'],
                        'first_publish_year' => 1925,
                        'first_sentence'    => ['In my younger and more vulnerable years...'],
                    ],
                ],
            ], 200),
        ]);

        $results = $this->service->searchByTitle('The Great Gatsby');

        $this->assertIsArray($results);
        $this->assertCount(1, $results);

        $book = $results[0];
        $this->assertEquals('The Great Gatsby', $book['title']);
        $this->assertEquals('F. Scott Fitzgerald', $book['author']);
        $this->assertEquals('9780743273565', $book['isbn']);
        $this->assertEquals(1925, $book['published_year']);
        $this->assertArrayHasKey('cover_url', $book);
        $this->assertArrayHasKey('description', $book);
    }

    public function test_search_by_title_throws_exception_on_non_200_response(): void
    {
        Http::fake([
            'openlibrary.org/search.json*' => Http::response([], 503),
        ]);

        $this->expectException(\Exception::class);

        $this->service->searchByTitle('Anything');
    }

    public function test_fetch_by_isbn_returns_normalized_book_data(): void
    {
        $isbn = '9780743273565';

        Http::fake([
            'openlibrary.org/api/books*' => Http::response([
                "ISBN:{$isbn}" => [
                    'title'       => 'The Great Gatsby',
                    'authors'     => [['name' => 'F. Scott Fitzgerald']],
                    'description' => 'A novel set in the Jazz Age.',
                    'cover'       => ['medium' => "https://covers.openlibrary.org/b/isbn/{$isbn}-M.jpg"],
                    'publish_date' => '1925',
                ],
            ], 200),
        ]);

        $book = $this->service->fetchByISBN($isbn);

        $this->assertIsArray($book);
        $this->assertEquals('The Great Gatsby', $book['title']);
        $this->assertEquals('F. Scott Fitzgerald', $book['author']);
        $this->assertEquals($isbn, $book['isbn']);
        $this->assertEquals('A novel set in the Jazz Age.', $book['description']);
        $this->assertEquals('1925', $book['published_year']);
        $this->assertStringContainsString($isbn, $book['cover_url']);
    }
}
