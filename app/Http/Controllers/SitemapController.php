<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index()
    {
        $xml = Cache::remember('sitemap.xml', now()->addHours(6), function () {
            $urls = [
                ['loc' => url('/'),              'changefreq' => 'daily',   'priority' => '1.0'],
                ['loc' => url('/categories'),    'changefreq' => 'daily',   'priority' => '0.9'],
                ['loc' => url('/authors'),       'changefreq' => 'weekly',  'priority' => '0.7'],
                ['loc' => url('/bestsellers'),   'changefreq' => 'daily',   'priority' => '0.8'],
                ['loc' => url('/series'),        'changefreq' => 'weekly',  'priority' => '0.6'],
                ['loc' => url('/contact'),       'changefreq' => 'yearly',  'priority' => '0.3'],
                ['loc' => url('/faq'),           'changefreq' => 'monthly', 'priority' => '0.3'],
            ];

            Product::query()
                ->select(['slug', 'updated_at'])
                ->orderByDesc('updated_at')
                ->chunk(500, function ($products) use (&$urls) {
                    foreach ($products as $product) {
                        $urls[] = [
                            'loc'        => url('/product/' . $product->slug),
                            'lastmod'    => optional($product->updated_at)->toAtomString(),
                            'changefreq' => 'weekly',
                            'priority'   => '0.8',
                        ];
                    }
                });

            Category::query()
                ->select(['slug', 'updated_at'])
                ->get()
                ->each(function ($category) use (&$urls) {
                    $urls[] = [
                        'loc'        => url('/categories') . '?category=' . urlencode($category->slug),
                        'lastmod'    => optional($category->updated_at)->toAtomString(),
                        'changefreq' => 'weekly',
                        'priority'   => '0.7',
                    ];
                });

            Author::query()
                ->select(['slug', 'updated_at'])
                ->get()
                ->each(function ($author) use (&$urls) {
                    $urls[] = [
                        'loc'        => url('/categories') . '?author=' . urlencode($author->slug),
                        'lastmod'    => optional($author->updated_at)->toAtomString(),
                        'changefreq' => 'monthly',
                        'priority'   => '0.5',
                    ];
                });

            $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
            foreach ($urls as $entry) {
                $xml .= "  <url>\n";
                $xml .= '    <loc>' . htmlspecialchars($entry['loc'], ENT_XML1) . "</loc>\n";
                if (! empty($entry['lastmod'])) {
                    $xml .= '    <lastmod>' . $entry['lastmod'] . "</lastmod>\n";
                }
                $xml .= '    <changefreq>' . $entry['changefreq'] . "</changefreq>\n";
                $xml .= '    <priority>' . $entry['priority'] . "</priority>\n";
                $xml .= "  </url>\n";
            }
            $xml .= '</urlset>' . "\n";

            return $xml;
        });

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
