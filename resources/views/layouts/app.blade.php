<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shelf-E | Your Favorite Books</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Custom Scrollbar for a premium feel */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        /* Smooth reveal utility class */
        .reveal-loaded { opacity: 1 !important; transform: translateY(0) !important; }
        
        /* NEW: Ultra-light Epoxy Resin Fluid Background */
        .bg-resin {
            background-color: #f8fafc; /* Very light gray/slate base */
            background-image:
                radial-gradient(at 10% 20%, rgba(207, 250, 254, 0.7) 0px, transparent 50%), /* Soft Cyan */
                radial-gradient(at 90% 10%, rgba(255, 237, 213, 0.5) 0px, transparent 50%), /* Soft Orange */
                radial-gradient(at 30% 60%, rgba(236, 254, 255, 0.8) 0px, transparent 50%), /* Lighter Cyan */
                radial-gradient(at 80% 80%, rgba(207, 250, 254, 0.6) 0px, transparent 50%), /* Soft Cyan */
                radial-gradient(at 50% 90%, rgba(255, 247, 237, 0.7) 0px, transparent 50%), /* Lighter Orange */
                radial-gradient(at 0% 100%, rgba(207, 250, 254, 0.5) 0px, transparent 50%);
            background-attachment: fixed; /* Keeps the fluid effect still while scrolling */
        }
    </style>
</head>


<body class="bg-background text-foreground font-sans antialiased">

    <div class="relative z-50 px-4 py-2 text-xs font-medium tracking-wide text-white bg-cyan-900 sm:text-sm">
        <div class="container flex items-center justify-between mx-auto">
            <div class="flex items-center space-x-4">
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    Free Standard Shipping on orders over ৳1000!
                </span>
            </div>
            <div class="hidden space-x-6 md:flex">
                <a href="#" class="transition-colors hover:text-cyan-200">Help Center</a>
                <a href="#" class="transition-colors hover:text-cyan-200">Track Order</a>
            </div>
        </div>
    </div>
    
    <header x-data="{ scrolled: false, mobileMenuOpen: false }" 
            @scroll.window="scrolled = (window.pageYOffset > 20)" 
            :class="scrolled ? 'shadow-md bg-white/95 backdrop-blur-md sticky top-0 z-50' : 'bg-white border-b border-gray-200'" 
            class="top-0 z-50 w-full transition-all duration-300">
        
        <div class="container px-4 py-3 mx-auto transition-all duration-300" :class="scrolled ? 'md:py-2' : 'md:py-4'">
            <div class="flex items-center justify-between">
                
                <div class="flex-shrink-0 group">
                    <a href="/" class="inline-block text-2xl font-bold text-gray-900 transition-transform duration-300 group-hover:scale-105">
                        <span class="transition-colors duration-300 text-cyan-500 group-hover:text-cyan-600">Shelf</span>-E
                    </a>
                </div>

                <div class="relative flex-1 hidden max-w-xl mx-8 md:block" x-data="liveSearch()">
                    <form action="{{ route('categories.index') }}" method="GET" class="relative w-full group">
                        <input type="text" name="search" placeholder="Search by Titles or Authors..." 
                               x-model="query" 
                               @input.debounce.300ms="fetchResults" 
                               @focus="if(query.length >= 2) showDropdown = true" 
                               @click.away="showDropdown = false"
                               autocomplete="off"
                               class="w-full px-5 py-2.5 transition-all duration-300 border border-gray-300 rounded-full focus:outline-none focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 bg-gray-50 focus:bg-white text-sm">
                        <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-cyan-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </form>

                    <div x-show="showDropdown" x-transition style="display: none;" class="absolute z-50 w-full mt-2 overflow-hidden overflow-y-auto bg-white border border-gray-100 shadow-2xl rounded-xl max-h-96">
                        <div x-show="loading" class="p-4 text-sm text-center text-gray-500">Searching...</div>
                        <div x-show="!loading && results.length === 0 && query.length >= 2" class="p-4 text-sm text-center text-gray-500">No books found matching "<span x-text="query"></span>"</div>
                        
                        <template x-for="book in results" :key="book.id">
                            <a :href="'/product/' + book.slug" class="flex items-center p-3 transition-colors border-b hover:bg-cyan-50 border-gray-50 last:border-b-0">
                                <div class="w-10 overflow-hidden bg-gray-200 rounded h-14 shrink-0">
                                    <img x-show="book.image_path" :src="'/storage/' + book.image_path" class="object-cover w-full h-full">
                                    <div x-show="!book.image_path" class="flex items-center justify-center w-full h-full text-[8px] text-gray-400 uppercase">Cover</div>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-bold text-gray-900" x-text="book.title"></h4>
                                    <p class="text-xs text-gray-500" x-text="book.author"></p>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3 sm:space-x-6">
                    <a href="/checkout" class="relative p-2 text-gray-600 transition-colors hover:text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        @if(session('cart') && count(session('cart')) > 0)
                            <span class="absolute flex items-center justify-center w-5 h-5 text-xs font-bold text-white rounded-full bg-cyan-500 top-0 right-0 border-2 border-white shadow-sm animate-[bounce_1s_ease-in-out_infinite]">
                                {{ count(session('cart')) }}
                            </span>
                        @endif
                    </a>
                    
                    @guest
                        <div class="items-center hidden space-x-3 md:flex">
                            <a href="{{ route('login') }}"
                            class="px-6 py-2.5 text-sm font-bold text-white transition-all bg-orange-500 rounded-full hover:bg-orange-600 hover:-translate-y-0.5 shadow-sm">
                                Login
                            </a>

                            <a href="{{ route('register') }}"
                            class="px-6 py-2.5 text-sm font-bold text-cyan-700 transition-all bg-cyan-100 rounded-full hover:bg-cyan-200 hover:-translate-y-0.5 shadow-sm">
                                Register
                            </a>
                        </div>
                    @endguest

                    @auth
                        <div class="relative hidden md:block" x-data="{ userMenuOpen: false }">
                            <button @click="userMenuOpen = !userMenuOpen" @click.away="userMenuOpen = false" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-full shadow-sm hover:bg-cyan-50">
                                <span class="truncate max-w-[120px]">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 ml-2 text-gray-400" :class="userMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="userMenuOpen" x-transition.opacity.duration.200ms class="absolute right-0 z-50 w-48 mt-2 bg-white border border-gray-100 shadow-xl rounded-xl" style="display: none;">
                                <div class="p-2 space-y-1">
                                    <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 rounded-lg hover:bg-cyan-50">Dashboard</a>
                                    <form method="POST" action="{{ route('logout') }}" class="pt-1 mt-1 border-t border-gray-100">
                                        @csrf
                                        <button type="submit" class="block w-full px-4 py-2 text-sm text-left text-red-600 rounded-lg hover:bg-red-50">Log Out</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endauth

                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 text-gray-600 rounded-md md:hidden hover:bg-gray-100 focus:outline-none">
                        <svg class="w-6 h-6" x-show="!mobileMenuOpen" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        <svg class="w-6 h-6" x-show="mobileMenuOpen" style="display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            {{-- MOBILE NAVIGATION FIX --}}
            <div class="relative mt-4 md:hidden" x-data="liveSearch()">
                <form action="{{ route('categories.index') }}" method="GET" class="relative w-full">
                    <input type="text" name="search" placeholder="Search books..." 
                           x-model="query" 
                           @input.debounce.300ms="fetchResults" 
                           @focus="if(query.length >= 2) showDropdown = true" 
                           @click.away="showDropdown = false"
                           autocomplete="off"
                           class="w-full px-4 py-2 text-sm border border-gray-300 rounded-full focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 bg-gray-50">
                    <button type="submit" class="absolute text-gray-400 right-3 top-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </form>

                <div x-show="showDropdown" x-transition style="display: none;" class="absolute z-50 w-full mt-2 overflow-hidden overflow-y-auto bg-white border border-gray-100 shadow-2xl rounded-xl max-h-80">
                    <div x-show="loading" class="p-4 text-sm text-center text-gray-500">Searching...</div>
                    <div x-show="!loading && results.length === 0 && query.length >= 2" class="p-4 text-sm text-center text-gray-500">No books found matching "<span x-text="query"></span>"</div>
                    
                    <template x-for="book in results" :key="book.id">
                        <a :href="'/product/' + book.slug" class="flex items-center p-3 transition-colors border-b hover:bg-cyan-50 border-gray-50 last:border-b-0">
                            <div class="w-10 overflow-hidden bg-gray-200 rounded h-14 shrink-0">
                                <img x-show="book.image_path" :src="'/storage/' + book.image_path" class="object-cover w-full h-full">
                                <div x-show="!book.image_path" class="flex items-center justify-center w-full h-full text-[8px] text-gray-400 uppercase">Cover</div>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-bold text-gray-900" x-text="book.title"></h4>
                                <p class="text-xs text-gray-500" x-text="book.author"></p>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
        </div>

        {{-- DESKTOP NAVIGATION FIX --}}
        <nav class="container hidden px-4 mx-auto space-x-8 text-sm font-medium text-gray-600 border-t border-gray-100 md:flex md:justify-center">
            <div class="relative flex items-center group">
                <a href="{{ route('categories.index') }}" class="flex items-center py-4 font-medium text-gray-700 transition-colors hover:text-cyan-600">
                    Categories
                    <svg class="w-4 h-4 ml-1 transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </a>

                <div class="absolute left-0 z-50 invisible transition-all duration-300 transform translate-y-2 opacity-0 top-full w-[600px] group-hover:translate-y-0 group-hover:opacity-100 group-hover:visible">
                    <div class="pt-2">
                        <div class="overflow-hidden bg-white border border-gray-100 shadow-2xl rounded-xl">
                            <div class="p-8">
                                <h3 class="mb-4 text-xs font-bold tracking-wider text-gray-400 uppercase">Browse Collections</h3>
                                
                                <div class="grid grid-cols-3 gap-x-8 gap-y-4">
                                    @if(isset($globalCategories))
                                        @foreach($globalCategories as $category)
                                            <a href="{{ route('categories.index', ['category' => $category->slug]) }}" 
                                            class="flex items-center text-sm font-medium text-gray-600 transition-colors hover:text-cyan-600 group/link">
                                            <span class="w-1.5 h-1.5 mr-2 rounded-full bg-gray-200 transition-colors group-hover/link:bg-cyan-500"></span>
                                            {{ $category->name }}
                                            </a>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="px-8 py-4 border-t border-gray-100 bg-gray-50">
                                <a href="{{ route('categories.index') }}" class="flex items-center justify-center text-sm font-bold text-orange-500 transition-colors hover:text-orange-600">
                                    See All Books <span class="ml-1" aria-hidden="true">&rarr;</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <a href="/authors" class="flex items-center py-4 transition-colors hover:text-orange-500">Authors</a>
    
            <a href="/categories?min_rating=4" class="flex items-center py-4 transition-colors hover:text-orange-500">Bestsellers</a>
            
            <a href="/contact" class="flex items-center py-4 transition-colors hover:text-orange-500">Contact</a>
            
            <a href="#newsletter" class="flex items-center py-4 transition-colors hover:text-orange-500">Newsletter</a>
        </nav>

        {{-- MOBILE NAVIGATION FIX --}}
        <div x-show="mobileMenuOpen" x-transition.duration.300ms class="bg-white border-t border-gray-100 md:hidden" style="display: none;">
            <div class="px-4 pt-2 pb-4 space-y-1 shadow-inner">
                
                {{-- Alpine.js Accordion for Categories --}}
                <div x-data="{ openCategories: false }" class="w-full">
                    <button type="button" @click="openCategories = !openCategories" class="flex items-center justify-between w-full px-3 py-2 text-base font-medium text-left text-gray-700 transition-colors rounded-md hover:text-cyan-600 hover:bg-cyan-50">
                        Categories
                        <svg class="w-5 h-5 transition-transform duration-200" :class="{'rotate-180': openCategories}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="openCategories"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         style="display: none;"
                         class="pb-2 pl-6 mt-1 space-y-3">
                        @if(isset($globalCategories))
                            @foreach($globalCategories as $category)
                                <a href="{{ route('categories.index', ['category' => $category->slug]) }}"
                                   @click="mobileMenuOpen = false"
                                   class="block text-sm font-medium text-gray-500 transition-colors hover:text-cyan-600">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        @endif
                        <a href="{{ route('categories.index') }}"
                           @click="mobileMenuOpen = false"
                           class="block pt-2 text-sm font-bold text-orange-500 transition-colors hover:text-orange-600">
                            See All Books &rarr;
                        </a>
                    </div>
                </div>

                <a href="/authors"      @click="mobileMenuOpen = false" class="block px-3 py-2 text-base font-medium text-gray-700 transition-colors rounded-md hover:text-orange-500 hover:bg-orange-50">Authors</a>
                <a href="/categories?min_rating=4" @click="mobileMenuOpen = false" class="block px-3 py-2 text-base font-medium text-gray-700 transition-colors rounded-md hover:text-orange-500 hover:bg-orange-50">Bestsellers</a>
                <a href="/contact"      @click="mobileMenuOpen = false" class="block px-3 py-2 text-base font-medium text-gray-700 transition-colors rounded-md hover:text-orange-500 hover:bg-orange-50">Contact</a>
                <a href="#newsletter"   @click="mobileMenuOpen = false" class="block px-3 py-2 text-base font-medium text-gray-700 transition-colors rounded-md hover:text-orange-500 hover:bg-orange-50">Newsletter</a>

                @auth
                    <div class="pt-4 mt-4 border-t border-gray-200">
                        <div class="px-3 mb-2 text-xs font-bold text-gray-400 uppercase">
                            Account ({{ auth()->user()->name }})
                        </div>

                        <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}"
                           @click="mobileMenuOpen = false"
                           class="block px-3 py-2 text-base font-medium text-gray-700 rounded-md hover:text-cyan-600 hover:bg-cyan-50">
                            Dashboard
                        </a>

                        <a href="{{ route('wishlist.index') }}"
                           @click="mobileMenuOpen = false"
                           class="block px-3 py-2 text-base font-medium text-gray-700 rounded-md hover:text-cyan-600 hover:bg-cyan-50">
                            My Wishlist
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-3 py-2 text-base font-medium text-left text-red-600 rounded-md hover:bg-red-50">
                                Log Out
                            </button>
                        </form>
                    </div>
                @else
                    <div class="pt-4 mt-4 space-y-2 border-t border-gray-200">
                        <a href="{{ route('login') }}"
                           class="block w-full px-4 py-2 font-bold text-center text-white bg-orange-500 rounded-md hover:bg-orange-600">
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                           class="block w-full px-4 py-2 font-bold text-center rounded-md text-cyan-700 bg-cyan-100 hover:bg-cyan-200">
                            Register
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-grow w-full transition-all duration-700 ease-out translate-y-4 opacity-0" 
          x-data="{ loaded: false }" 
          x-init="setTimeout(() => loaded = true, 100)" 
          :class="loaded ? 'reveal-loaded' : ''">
        @yield('content')
    </main>

    <footer class="mt-auto bg-white border-t border-gray-200">
        {{-- ADDED: id="newsletter" --}}
        <div id="newsletter" class="relative py-16 overflow-hidden bg-cyan-600">
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none opacity-10">
                <svg class="absolute transform rotate-45 w-96 h-96 -top-10 -right-10" fill="currentColor" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"/></svg>
            </div>
            
            <div class="container relative z-10 px-4 mx-auto text-center">
                <h2 class="mb-3 text-3xl font-extrabold tracking-tight text-white">Get your first discount!</h2>
                <p class="mb-8 font-medium text-cyan-100">Subscribe to our newsletter and get a 15% discount code</p>
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="flex max-w-md p-1.5 mx-auto bg-white/20 backdrop-blur-md rounded-full border border-white/30 focus-within:bg-white focus-within:ring-4 focus-within:ring-cyan-500/30 transition-all duration-500">
                    @csrf
                    <input type="email" name="email" placeholder="Your email address" required class="flex-1 px-5 text-white transition-colors bg-transparent border-none placeholder-cyan-100 focus:ring-0 focus:outline-none focus:text-gray-900 focus:placeholder-gray-400">
                    <button type="submit" class="px-8 py-3 text-sm font-bold text-white uppercase tracking-wider transition-all duration-300 bg-orange-500 rounded-full hover:bg-orange-600 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
        
        <div class="py-16 bg-gray-50">
            <div class="container grid grid-cols-1 gap-12 px-4 mx-auto md:grid-cols-4">
                <div class="md:col-span-1">
                    <a href="/" class="inline-block mb-4 text-3xl font-extrabold tracking-tight text-gray-900 transition-transform hover:scale-105">
                        <span class="text-cyan-500">Shelf</span>-E
                    </a>
                    <p class="mb-6 text-sm leading-relaxed text-gray-500">Your premium destination for discovering worlds through words. Curated collections for the modern reader.</p>
                </div>
                <div>
                    <h4 class="mb-5 text-sm font-bold tracking-wider text-gray-900 uppercase">Shop</h4>
                    <ul class="space-y-3 text-sm text-gray-500">
                        <li><a href="#" class="flex items-center transition-colors duration-200 hover:text-cyan-600 group"><span class="w-2 h-px mr-2 transition-opacity opacity-0 bg-cyan-500 group-hover:opacity-100"></span> New Arrivals</a></li>
                        <li><a href="#" class="flex items-center transition-colors duration-200 hover:text-cyan-600 group"><span class="w-2 h-px mr-2 transition-opacity opacity-0 bg-cyan-500 group-hover:opacity-100"></span> Bestsellers</a></li>
                        <li><a href="#" class="flex items-center transition-colors duration-200 hover:text-cyan-600 group"><span class="w-2 h-px mr-2 transition-opacity opacity-0 bg-cyan-500 group-hover:opacity-100"></span> Sale Items</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="mb-5 text-sm font-bold tracking-wider text-gray-900 uppercase">Support</h4>
                    <ul class="space-y-3 text-sm text-gray-500">
                        <li><a href="#" class="flex items-center transition-colors duration-200 hover:text-cyan-600 group"><span class="w-2 h-px mr-2 transition-opacity opacity-0 bg-cyan-500 group-hover:opacity-100"></span> Track Order</a></li>
                        <li><a href="#" class="flex items-center transition-colors duration-200 hover:text-cyan-600 group"><span class="w-2 h-px mr-2 transition-opacity opacity-0 bg-cyan-500 group-hover:opacity-100"></span> Returns Policy</a></li>
                        <li><a href="#" class="flex items-center transition-colors duration-200 hover:text-cyan-600 group"><span class="w-2 h-px mr-2 transition-opacity opacity-0 bg-cyan-500 group-hover:opacity-100"></span> FAQ</a></li>
                    </ul>
                </div>
                
                {{-- ADDED: id="contact" --}}
                <div id="contact">
                    <h4 class="mb-5 text-sm font-bold tracking-wider text-gray-900 uppercase">Contact</h4>
                    <ul class="space-y-3 text-sm text-gray-500">
                        <li class="flex items-center"><svg class="w-4 h-4 mr-3 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg> +880 1234 567890</li> 
                        <li class="flex items-center"><svg class="w-4 h-4 mr-3 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg> support@shelf-e.com</li>
                        <li class="flex items-start"><svg class="w-4 h-4 mt-1 mr-3 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> Dhaka, Bangladesh</li>
                    </ul>
                </div>
            </div>
            
            <div class="container flex flex-col items-center justify-between px-4 pt-8 mx-auto mt-12 text-xs text-center text-gray-400 border-t border-gray-200 md:flex-row">
                <p>&copy; {{ date('Y') }} Shelf-E. All rights reserved.</p>
                <div class="flex mt-4 space-x-6 md:mt-0">
                    <a href="#" class="transition-colors hover:text-cyan-500">Privacy Policy</a>
                    <a href="#" class="transition-colors hover:text-cyan-500">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>


@if(session('error'))
    <div x-data="{ show: true }"
         x-init="setTimeout(() => show = false, 2000)"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed z-[9999] bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-3 px-5 py-3 bg-red-600 text-white text-sm font-semibold rounded-xl shadow-lg pointer-events-none"
         style="display: none;">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        {{ session('error') }}
    </div>
@endif

<script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('liveSearch', () => ({
                query: '',
                results: [],
                loading: false,
                showDropdown: false,

                async fetchResults() {
                    if (this.query.length < 2) {
                        this.results = [];
                        this.showDropdown = false;
                        return;
                    }
                    
                    this.loading = true;
                    this.showDropdown = true;
                    
                    try {
                        // We will build this backend route next!
                        const response = await fetch(`/api/search-books?q=${encodeURIComponent(this.query)}`);
                        const data = await response.json();
                        this.results = data;
                    } catch (error) {
                        console.error('Search failed:', error);
                        this.results = [];
                    } finally {
                        this.loading = false;
                    }
                }
            }));
        });
    </script>
</body>
</html>