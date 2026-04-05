<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Shelf-E') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-background text-foreground">
        <div class="flex flex-col items-center pt-16 pb-12 bg-gray-50 sm:justify-center">

            <div class="mb-8">
                <a href="/" class="text-3xl font-bold text-gray-900">
                    Shelf-E
                </a>
            </div>

            <div class="w-full px-10 py-10 mt-6 overflow-hidden bg-white border border-gray-100 shadow-xl sm:max-w-md sm:rounded-3xl">
                {{ $slot }}
            </div>
        </div>

        <footer class="mt-auto border-t border-gray-100 bg-gray-50">
            <div class="container grid grid-cols-1 gap-8 px-4 py-12 mx-auto md:grid-cols-4">
                <div>
                    <a href="/" class="text-2xl font-bold text-gray-900">Shelf-E</a>
                    <p class="mt-4 text-sm text-gray-500">Your favorite books, delivered to your doorstep.</p>
                </div>
                <div>
                    <h4 class="mb-4 font-bold text-gray-900">Browse</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><a href="/categories" class="transition hover:text-gray-900">Categories</a></li>
                        <li><a href="#" class="transition hover:text-gray-900">Authors</a></li>
                        <li><a href="#" class="transition hover:text-gray-900">Blog</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="mb-4 font-bold text-gray-900">Social</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><a href="#" class="transition hover:text-gray-900">Facebook</a></li>
                        <li><a href="#" class="transition hover:text-gray-900">Instagram</a></li>
                        <li><a href="#" class="transition hover:text-gray-900">Twitter</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="mb-4 font-bold text-gray-900">Contact</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li>+880 1234 567890</li>
                        <li>support@shelf-e.com</li>
                        <li>Dhaka, Bangladesh</li>
                    </ul>
                </div>
            </div>
            <div class="py-6 text-center border-t border-gray-200">
                <p class="text-xs text-gray-400">© 2026 Shelf-E Bookstore. All rights reserved.</p>
            </div>
        </footer>
    </body>
</html>
