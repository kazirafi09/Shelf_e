<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - Shelf-E</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-background text-foreground">

<div class="flex min-h-screen font-sans" x-data="{ sidebarOpen: false }">
    
    {{-- Mobile Overlay --}}
    <div x-show="sidebarOpen" 
         x-transition.opacity.duration.300ms
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-gray-900/80 backdrop-blur-sm lg:hidden" style="display: none;"></div>

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 h-screen text-white transition-transform duration-300 ease-in-out transform bg-gray-900 shadow-2xl shrink-0 lg:translate-x-0 lg:sticky lg:top-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        
        <div class="flex items-center justify-between p-6 shrink-0">
            <h2 class="text-2xl font-bold tracking-tight"><span class="text-cyan-400">Admin</span>Panel</h2>
            <button @click="sidebarOpen = false" class="text-gray-400 lg:hidden hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <nav class="flex flex-col flex-1 px-4 mt-2 overflow-y-auto">
            <div class="space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 font-medium rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-cyan-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.books.index') }}" class="flex items-center px-4 py-3 font-medium transition rounded-lg {{ request()->routeIs('admin.books.*') ? 'bg-cyan-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    Manage Books
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-3 font-medium transition rounded-lg {{ request()->routeIs('admin.orders.*') ? 'bg-cyan-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    Orders
                </a>
                <a href="{{ route('admin.hero-slides.index') }}" class="flex items-center px-4 py-3 font-medium transition rounded-lg {{ request()->routeIs('admin.hero-slides.*') ? 'bg-cyan-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Manage Slides
                </a>

                {{-- V2 Links --}}
                <a href="{{ route('admin.authors.index') }}" class="flex items-center px-4 py-3 font-medium transition rounded-lg {{ request()->routeIs('admin.authors.*') ? 'bg-cyan-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Authors
                </a>

                <a href="{{ route('admin.reviews.index') }}" class="flex items-center px-4 py-3 font-medium transition rounded-lg {{ request()->routeIs('admin.reviews.*') ? 'bg-cyan-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    Reviews
                </a>

                <a href="{{ route('admin.coins.index') }}" class="flex items-center px-4 py-3 font-medium transition rounded-lg {{ request()->routeIs('admin.coins.*') ? 'bg-cyan-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Coin Ledger
                </a>

                <a href="{{ route('admin.scraper.index') }}" class="flex items-center px-4 py-3 font-medium transition rounded-lg {{ request()->routeIs('admin.scraper.*') ? 'bg-cyan-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Book Scraper
                </a>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="pt-10 pb-6 mt-auto">
                @csrf
                <button type="submit" class="flex items-center w-full px-4 py-3 font-medium text-left text-red-400 transition rounded-lg hover:bg-gray-800 hover:text-red-300">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Log Out
                </button>
            </form>
        </nav>
    </aside>

    {{-- Main Content Area --}}
    <main class="flex-1 min-w-0">
        {{-- Mobile Header --}}
        <div class="flex items-center justify-between px-4 py-4 bg-white border-b border-gray-200 lg:hidden">
            <h2 class="text-xl font-bold text-gray-900"><span class="text-cyan-500">Shelf</span>-E Admin</h2>
            <button @click="sidebarOpen = true" class="p-2 -mr-2 text-gray-600 rounded-md hover:bg-gray-100 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>

        <div class="p-4 mx-auto sm:p-6 lg:p-8 max-w-7xl">
            {{-- Dynamic Page Header --}}
            <header class="mb-8">
                <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 sm:text-3xl">@yield('title')</h1>
                <p class="mt-1 text-sm text-gray-500">@yield('subtitle')</p>
            </header>

            {{-- THE CONTENT GOES HERE --}}
            @yield('admin-content')
        </div>
    </main>
</div>

</body>
</html>