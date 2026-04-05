<?php

namespace Tests\Unit\Services;

use App\Services\BookScraperService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Unit tests for BookScraperService.
 *
 * The service was rewritten from OpenLibrary to Booktank BD (a Shopify store).
 * All HTTP calls are faked so no real network traffic is made.
 *
 * Public interface:
 *   searchByTitle(string $query): array
 *   fetchProductDetails(string $handle): array
 *   downloadCover(string $url, string $slug): ?string
 */
class BookScraperServiceTest extends TestCase
{
    private BookScraperService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BookScraperService();
    }

    // ── searchByTitle ─────────────────────────────────────────────────────────

    /** @test */
    public function search_by_title_returns_normalized_result_array(): void
    {
        Http::fake([
            'booktankbd.com/search/suggest.json*' => Http::response([
                'resources' => [
                    'results' => [
                        'products' => [
                            [
                                'title' => 'The Great Gatsby by F. Scott Fitzgerald',
                                'image' => ['url' => 'https://cdn.booktankbd.com/gatsby.jpg'],
                                'url'   => '/products/the-great-gatsby',
                                'price' => '500.00',
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $results = $this->service->searchByTitle('The Great Gatsby');

        $this->assertIsArray($results);
        $this->assertCount(1, $results);

        $book = $results[0];
        // Title and author are split on " by "
        $this->assertEquals('The Great Gatsby', $book['title']);
        $this->assertEquals('F. Scott Fitzgerald', $book['author']);
        // Handle is extracted from the product URL path
        $this->assertEquals('the-great-gatsby', $book['handle']);
        $this->assertEquals('500.00', $book['price']);
        $this->assertEquals('https://cdn.booktankbd.com/gatsby.jpg', $book['cover_url']);
    }

    /** @test */
    public function search_by_title_returns_empty_array_when_no_products_found(): void
    {
        Http::fake([
            'booktankbd.com/search/suggest.json*' => Http::response([
                'resources' => ['results' => ['products' => []]],
            ], 200),
        ]);

        $results = $this->service->searchByTitle('Nonexistent Book');

        $this->assertIsArray($results);
        $this->assertEmpty($results);
    }

    /** @test */
    public function search_by_title_throws_exception_on_failed_response(): void
    {
        Http::fake([
            'booktankbd.com/search/suggest.json*' => Http::response([], 503),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/search failed/i');

        $this->service->searchByTitle('Anything');
    }

    /** @test */
    public function search_result_splits_title_and_author_on_last_by_keyword(): void
    {
        // A title that contains " by " in the book title itself should split on
        // the LAST occurrence so the author is correctly identified.
        Http::fake([
            'booktankbd.com/search/suggest.json*' => Http::response([
                'resources' => [
                    'results' => [
                        'products' => [[
                            'title' => 'Murder on the Orient Express by Agatha Christie',
                            'image' => ['url' => null],
                            'url'   => '/products/orient-express',
                            'price' => null,
                        ]],
                    ],
                ],
            ], 200),
        ]);

        $results = $this->service->searchByTitle('Orient Express');

        $this->assertEquals('Murder on the Orient Express', $results[0]['title']);
        $this->assertEquals('Agatha Christie', $results[0]['author']);
        $this->assertEquals('orient-express', $results[0]['handle']);
    }

    /** @test */
    public function search_result_author_is_null_when_no_by_separator_exists(): void
    {
        Http::fake([
            'booktankbd.com/search/suggest.json*' => Http::response([
                'resources' => [
                    'results' => [
                        'products' => [[
                            'title' => 'Just A Title With No Author',
                            'image' => ['url' => null],
                            'url'   => '/products/just-a-title',
                            'price' => null,
                        ]],
                    ],
                ],
            ], 200),
        ]);

        $results = $this->service->searchByTitle('Just A Title');

        $this->assertEquals('Just A Title With No Author', $results[0]['title']);
        $this->assertNull($results[0]['author']);
    }

    // ── fetchProductDetails ───────────────────────────────────────────────────

    /** @test */
    public function fetch_product_details_returns_description_tags_and_product_type(): void
    {
        Http::fake([
            'booktankbd.com/products/the-great-gatsby.json' => Http::response([
                'product' => [
                    'body_html'    => '<p>A tale of wealth and the American Dream set in the 1920s.</p>',
                    'tags'         => ['Fiction', 'Classic', 'American Literature'],
                    'product_type' => 'Novel',
                ],
            ], 200),
        ]);

        $details = $this->service->fetchProductDetails('the-great-gatsby');

        $this->assertIsArray($details);
        $this->assertEquals(
            'A tale of wealth and the American Dream set in the 1920s.',
            $details['description']
        );
        $this->assertNull($details['synopsis']); // short text, not split
        $this->assertContains('Fiction', $details['tags']);
        $this->assertContains('Classic', $details['tags']);
        $this->assertEquals('Novel', $details['product_type']);
    }

    /** @test */
    public function fetch_product_details_returns_empty_defaults_on_failed_response(): void
    {
        Http::fake([
            'booktankbd.com/products/missing-book.json' => Http::response([], 404),
        ]);

        $details = $this->service->fetchProductDetails('missing-book');

        $this->assertNull($details['description']);
        $this->assertNull($details['synopsis']);
        $this->assertEmpty($details['tags']);
        $this->assertNull($details['product_type']);
    }

    /** @test */
    public function fetch_product_details_strips_html_tags_from_body(): void
    {
        Http::fake([
            'booktankbd.com/products/html-book.json' => Http::response([
                'product' => [
                    'body_html'    => '<h2>About</h2><p><strong>A great book.</strong> Worth reading.</p>',
                    'tags'         => [],
                    'product_type' => '',
                ],
            ], 200),
        ]);

        $details = $this->service->fetchProductDetails('html-book');

        $this->assertStringNotContainsString('<', $details['description']);
        $this->assertStringContainsString('A great book.', $details['description']);
    }
}
