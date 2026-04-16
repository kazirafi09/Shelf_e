<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockBots
{
    /**
     * Known scraper / headless-browser User-Agent patterns.
     * Checked case-insensitively.
     */
    private const BLOCKED_AGENTS = [
        'python-requests',
        'python-urllib',
        'python-httpx',
        'aiohttp',
        'scrapy',
        'curl',
        'wget',
        'httpie',
        'go-http-client',
        'java/',
        'okhttp',
        'ruby',
        'perl',
        'libwww',
        'lwp-',
        'axios',
        'node-fetch',
        'node.js',
        'got/',
        'undici',
        'playwright',
        'puppeteer',
        'selenium',
        'phantomjs',
        'headlesschrome',
        'headless',
        'zgrab',
        'masscan',
        'sqlmap',
        'nikto',
        'dirbuster',
        'nmap',
        'dataforseo',
        'semrush',
        'ahrefsbot',
        'dotbot',
        'mj12bot',
        'petalbot',
        'yandexbot',
        'baiduspider',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $ua = strtolower($request->userAgent() ?? '');

        // Block empty / missing User-Agent
        if ($ua === '') {
            abort(403, 'Forbidden');
        }

        // Block known scraper signatures
        foreach (self::BLOCKED_AGENTS as $pattern) {
            if (str_contains($ua, $pattern)) {
                abort(403, 'Forbidden');
            }
        }

        // Real browsers always send an Accept header. Reject requests that don't.
        if (! $request->header('Accept')) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
