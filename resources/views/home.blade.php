@extends('layouts.app')

@section('content')

{{-- Rotating Quote — sits above the hero --}}
@if($quote)
<div class="relative border-b border-border bg-card overflow-hidden"
     x-data="{
         quoteText: {{ Js::from($quote->quote) }},
         quoteAuthor: {{ Js::from($quote->author) }},
         visible: true,
         progress: false,
         startQuoteRefresh() {
             this.progress = true;
             setInterval(() => {
                 this.visible = false;
                 setTimeout(() => {
                     fetch('/random-quote')
                         .then(r => r.json())
                         .then(data => {
                             this.quoteText   = data.quote;
                             this.quoteAuthor = data.author;
                             this.visible     = true;
                             this.progress    = false;
                             setTimeout(() => this.progress = true, 50);
                         });
                 }, 400);
             }, 10000);
         }
     }"
     x-init="startQuoteRefresh()">

    {{-- Auto-progress bar --}}
    <div class="absolute bottom-0 left-0 h-[2px] bg-gray-200 w-full">
        <div class="h-full bg-gray-400 origin-left"
             :class="progress ? 'transition-none' : ''"
             :style="progress ? 'animation: quote-progress 10s linear forwards;' : 'width:0'">
        </div>
    </div>

    <div class="container mx-auto max-w-3xl px-6 py-3 flex items-center justify-center gap-4"
         :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-1'"
         style="transition: opacity 0.4s ease, transform 0.4s ease;">

        <div class="text-center">
            <p class="text-sm md:text-base italic text-gray-600 leading-relaxed">
                <span class="text-xl font-serif not-italic text-gray-300 leading-none align-bottom">&ldquo;</span><span x-text="quoteText"></span><span class="text-xl font-serif not-italic text-gray-300 leading-none align-bottom">&rdquo;</span>
            </p>
            <p class="mt-1.5 text-[11px] font-semibold text-gray-400 uppercase tracking-widest not-italic">
                — <span x-text="quoteAuthor"></span>
            </p>
        </div>
    </div>
</div>

<style>
@keyframes quote-progress {
    from { width: 0%; }
    to   { width: 100%; }
}
</style>
@endif

{{-- Hero Section — full-bleed, outside the centered container --}}
<x-hero />

{{-- Main content container --}}
<div class="container px-4 mx-auto max-w-7xl" x-data="{ mounted: false }" x-init="setTimeout(() => mounted = true, 100)">

    <div class="grid grid-cols-1 gap-4 mt-8 transition-all duration-700 ease-out delay-300 transform md:grid-cols-4" :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
        <div class="flex items-center p-4 transition-shadow border shadow-sm bg-card text-card-foreground border-border rounded-2xl hover:shadow-md">
            <div class="flex items-center justify-center w-12 h-12 text-gray-700 bg-gray-100 rounded-full shrink-0"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg></div>
            <div class="ml-4">
                <h4 class="text-sm font-bold text-foreground">Fast Delivery</h4>
                <p class="text-[10px] text-muted-foreground uppercase tracking-wider">Nationwide shipping</p>
            </div>
        </div>
        <div class="flex items-center p-4 transition-shadow border shadow-sm bg-card text-card-foreground border-border rounded-2xl hover:shadow-md">
            <div class="flex items-center justify-center w-12 h-12 text-gray-700 bg-gray-100 rounded-full shrink-0"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg></div>
            <div class="ml-4">
                <h4 class="text-sm font-bold text-foreground">Secure Payment</h4>
                <p class="text-[10px] text-muted-foreground uppercase tracking-wider">100% safe checkout</p>
            </div>
        </div>
        <div class="flex items-center p-4 transition-shadow border shadow-sm bg-card text-card-foreground border-border rounded-2xl hover:shadow-md">
            <div class="flex items-center justify-center w-12 h-12 text-gray-700 bg-gray-100 rounded-full shrink-0"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg></div>
            <div class="ml-4">
                <h4 class="text-sm font-bold text-foreground">Easy Returns</h4>
                <p class="text-[10px] text-muted-foreground uppercase tracking-wider">7-day return policy</p>
            </div>
        </div>
        <div class="flex items-center p-4 transition-shadow border shadow-sm bg-card text-card-foreground border-border rounded-2xl hover:shadow-md">
            <div class="flex items-center justify-center w-12 h-12 text-gray-700 bg-gray-100 rounded-full shrink-0"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
            <div class="ml-4">
                <h4 class="text-sm font-bold text-foreground">Best Prices</h4>
                <p class="text-[10px] text-muted-foreground uppercase tracking-wider">Guaranteed lowest prices</p>
            </div>
        </div>
    </div>

    <div class="mt-20 transition-all duration-700 ease-out delay-700 transform" :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-extrabold tracking-tight text-foreground md:text-3xl">Shop By Format</h2>
        </div>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <a href="{{ route('categories.index') }}" class="relative p-8 overflow-hidden transition-colors border shadow-sm bg-card text-card-foreground border-border rounded-3xl group hover:border-gray-900 hover:shadow-lg">
                <div class="absolute top-0 right-0 p-6 transition-transform duration-500 opacity-5 group-hover:scale-110 group-hover:-rotate-12">
                    <svg class="w-32 h-32 text-gray-900" fill="currentColor" viewBox="0 0 20 20"><path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"></path></svg>
                </div>
                <h3 class="relative z-10 mb-2 text-xl font-bold text-foreground">Physical Books</h3>
                <p class="relative z-10 mb-4 text-sm text-muted-foreground">The classic feel of paper.</p>
                <span class="relative z-10 text-sm font-bold text-gray-700 group-hover:text-gray-900">Explore &rarr;</span>
            </a>
            <a href="#" class="relative p-8 overflow-hidden transition-colors border shadow-sm bg-card text-card-foreground border-border rounded-3xl group hover:border-gray-900 hover:shadow-lg">
                <div class="absolute top-0 right-0 p-6 transition-transform duration-500 opacity-5 group-hover:scale-110 group-hover:-rotate-12"><svg class="w-32 h-32 text-gray-900" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5zm11 1H6v8l4-2 4 2V6z" clip-rule="evenodd"></path></svg></div>
                <h3 class="relative z-10 mb-2 text-xl font-bold text-foreground">eBooks</h3>
                <p class="relative z-10 mb-4 text-sm text-muted-foreground">Read instantly on any device.</p>
                <span class="relative z-10 text-sm font-bold text-gray-700 group-hover:text-gray-900">Coming Soon &rarr;</span>
            </a>
            <a href="#" class="relative p-8 overflow-hidden transition-colors border shadow-sm bg-card text-card-foreground border-border rounded-3xl group hover:border-gray-900 hover:shadow-lg">
                <div class="absolute top-0 right-0 p-6 transition-transform duration-500 opacity-5 group-hover:scale-110 group-hover:-rotate-12"><svg class="w-32 h-32 text-gray-900" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 10a5.984 5.984 0 01-1.757 4.243 1 1 0 01-1.415-1.415A3.984 3.984 0 0013 10a3.983 3.983 0 00-1.172-2.828 1 1 0 010-1.415z" clip-rule="evenodd"></path></svg></div>
                <h3 class="relative z-10 mb-2 text-xl font-bold text-foreground">Audiobooks</h3>
                <p class="relative z-10 mb-4 text-sm text-muted-foreground">Listen to stories on the go.</p>
                <span class="relative z-10 text-sm font-bold text-gray-700 group-hover:text-gray-900">Coming Soon &rarr;</span>
            </a>
        </div>
    </div>

    <div class="mt-20 transition-all duration-700 ease-out delay-700 transform" :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
        <div class="flex items-end justify-between mb-8">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-foreground md:text-3xl">Popular Authors</h2>
                <p class="mt-1 text-sm text-muted-foreground">Discover the minds behind the masterpieces.</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($popularAuthors as $item)
            <a href="{{ route('categories.index', ['authors[]' => $item->author]) }}" class="flex items-center p-5 transition-all duration-300 border shadow-sm cursor-pointer bg-card text-card-foreground border-border rounded-2xl hover:shadow-md hover:-translate-y-1 group">
                
                <div class="w-16 h-16 overflow-hidden rounded-full shrink-0 transition-all duration-500 group-hover:scale-110 group-hover:shadow-inner">
                    @if($item->photo_path)
                        <img src="{{ asset('storage/' . $item->photo_path) }}" alt="{{ $item->author }}" class="object-cover w-full h-full">
                    @else
                        <div class="flex items-center justify-center w-full h-full text-2xl font-black text-white bg-gray-900 group-hover:bg-gray-800">
                            {{ substr($item->author, 0, 1) }}
                        </div>
                    @endif
                </div>
                
                <div class="ml-5 overflow-hidden">
                    <h3 class="text-lg font-bold truncate transition-colors duration-300 text-foreground group-hover:text-gray-900" title="{{ $item->author }}">{{ $item->author }}</h3>
                    <span class="inline-flex items-center mt-1 text-sm font-bold text-gray-700 transition-colors hover:text-gray-900">
                        View Books <svg class="w-4 h-4 ml-1 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </span>
                </div>
                
            </a>
            @endforeach
        </div>
    </div>

    <div class="mt-20 mb-10 transition-all duration-700 ease-out delay-700 transform"
         :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
        <div class="flex items-end justify-between mb-8">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-foreground md:text-3xl">Top Rated Books</h2>
                <p class="mt-1 text-sm text-muted-foreground">Handpicked favorites loved by our community.</p>
            </div>
            <a href="/categories" class="hidden text-sm font-bold tracking-wider text-gray-700 uppercase transition-colors hover:text-gray-900 md:inline-block">See All Collection &rarr;</a>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 md:gap-6">
            @foreach($topBooks as $book)
            <div class="relative flex flex-col p-4 overflow-hidden transition-all duration-500 border bg-card text-card-foreground border-border rounded-2xl group hover:shadow-xl hover:-translate-y-2 hover:border-gray-200">
                
                <div class="absolute z-10 top-2 right-2">
                    @auth
                        @php
                            // Assuming your loop variable is $book. Change to $product if your loop uses $product as $product!
                            $isWishlisted = \App\Models\Wishlist::where('user_id', auth()->id())
                                                                ->where('product_id', $book->id)
                                                                ->exists();
                        @endphp
                        <form action="{{ route('wishlist.toggle', $book->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="p-2 transition bg-white rounded-full shadow-sm hover:scale-110 active:scale-95">
                                <svg class="w-5 h-5 {{ $isWishlisted ? 'text-red-500 fill-current' : 'text-gray-400 fill-none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block p-2 transition bg-white rounded-full shadow-sm hover:scale-110">
                            <svg class="w-5 h-5 text-gray-400 fill-none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </a>
                    @endauth
                </div>
                
                <a href="/product/{{ $book->slug }}" class="z-10 block grow">
                    <div
                        x-data="{ zoomed: false }"
                        @mouseenter="zoomed = true"
                        @mouseleave="zoomed = false"
                        class="relative flex items-center justify-center w-full mb-4 overflow-hidden text-gray-400 bg-gray-100 shadow-sm aspect-[2/3] rounded-lg"
                    >
                        <div class="absolute inset-0 z-10 transition-opacity duration-300 pointer-events-none bg-black/0 group-hover:bg-black/5"></div>
                        @if($book->image_path)
                            <img src="{{ asset('storage/' . $book->image_path) }}" alt="{{ $book->title }}" loading="lazy"
                                 class="object-cover w-full h-full transition-transform duration-300"
                                 :class="{ 'scale-125 z-10': zoomed }">
                        @else
                            <span class="text-xs font-medium tracking-widest uppercase">Cover</span>
                        @endif
                    </div>
                    
                    <div class="flex items-center mb-1.5 space-x-1">
                        <svg class="w-4 h-4 text-yellow-400 drop-shadow-sm" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <span class="text-xs font-bold text-foreground">{{ number_format($book->approved_reviews_avg_rating ?? 0, 1) }}</span>
                    </div>
                    
                    <h3 class="font-bold truncate transition-colors duration-300 text-foreground group-hover:text-gray-900" title="{{ $book->title }}">{{ $book->title }}</h3>
                    <p class="mb-3 text-xs font-medium truncate text-muted-foreground">{{ $book->author }}</p>
                </a>
                
                <div class="z-10 flex flex-col justify-center pt-3 mt-auto border-t border-border h-[88px]">

                    <p class="text-lg font-extrabold transition-all duration-300 ease-out transform text-foreground group-hover:-translate-y-8 group-hover:opacity-0">
                        ৳ {{ number_format($book->display_price, 0) }}
                    </p>
                                    
                    <form action="{{ route('cart.add', $book->id) }}" method="POST" class="absolute transition-all duration-300 ease-out translate-y-8 opacity-0 left-4 right-4 bottom-4 group-hover:translate-y-0 group-hover:opacity-100">
                        @csrf
                        <button type="submit" class="w-full py-2.5 text-sm font-bold text-white transition-all bg-gray-900 shadow-md rounded-lg hover:bg-gray-800 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0">
                            Add to Cart
                        </button>
                    </form>

                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-8 text-center md:hidden">
            <a href="/categories" class="inline-block px-8 py-3 text-sm font-bold text-gray-700 transition-colors border-2 border-gray-200 rounded-full hover:bg-gray-100">See All Books</a>
        </div>
    </div>

    <div class="mt-20 mb-10 transition-all duration-700 ease-out delay-700 transform"
         :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
        <div class="flex items-end justify-between mb-8">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-foreground md:text-3xl">Bestsellers</h2>
                <p class="mt-1 text-sm text-muted-foreground">The most purchased books by our readers.</p>
            </div>
            <a href="{{ route('bestsellers.index') }}" class="hidden text-sm font-bold tracking-wider text-gray-700 uppercase transition-colors hover:text-gray-900 md:inline-block">See All Bestsellers &rarr;</a>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 md:gap-6">
            @foreach($bestSellers as $book)
            <div class="relative flex flex-col p-4 overflow-hidden transition-all duration-500 border bg-card text-card-foreground border-border rounded-2xl group hover:shadow-xl hover:-translate-y-2 hover:border-gray-200">

                <div class="absolute z-10 top-2 right-2">
                    @auth
                        @php
                            $isWishlisted = \App\Models\Wishlist::where('user_id', auth()->id())
                                                                ->where('product_id', $book->id)
                                                                ->exists();
                        @endphp
                        <form action="{{ route('wishlist.toggle', $book->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="p-2 transition bg-white rounded-full shadow-sm hover:scale-110 active:scale-95">
                                <svg class="w-5 h-5 {{ $isWishlisted ? 'text-red-500 fill-current' : 'text-gray-400 fill-none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block p-2 transition bg-white rounded-full shadow-sm hover:scale-110">
                            <svg class="w-5 h-5 text-gray-400 fill-none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </a>
                    @endauth
                </div>

                <a href="/product/{{ $book->slug }}" class="z-10 block grow">
                    <div
                        x-data="{ zoomed: false }"
                        @mouseenter="zoomed = true"
                        @mouseleave="zoomed = false"
                        class="relative flex items-center justify-center w-full mb-4 overflow-hidden text-gray-400 bg-gray-100 shadow-sm aspect-[2/3] rounded-lg"
                    >
                        <div class="absolute inset-0 z-10 transition-opacity duration-300 pointer-events-none bg-black/0 group-hover:bg-black/5"></div>
                        @if($book->image_path)
                            <img src="{{ asset('storage/' . $book->image_path) }}" alt="{{ $book->title }}" loading="lazy"
                                 class="object-cover w-full h-full transition-transform duration-300"
                                 :class="{ 'scale-125 z-10': zoomed }">
                        @else
                            <span class="text-xs font-medium tracking-widest uppercase">Cover</span>
                        @endif
                    </div>

                    <div class="flex items-center mb-1.5 space-x-1">
                        <svg class="w-4 h-4 text-yellow-400 drop-shadow-sm" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <span class="text-xs font-bold text-foreground">{{ number_format($book->approved_reviews_avg_rating ?? 0, 1) }}</span>
                    </div>

                    <h3 class="font-bold truncate transition-colors duration-300 text-foreground group-hover:text-gray-900" title="{{ $book->title }}">{{ $book->title }}</h3>
                    <p class="mb-3 text-xs font-medium truncate text-muted-foreground">{{ $book->author }}</p>
                </a>

                <div class="z-10 flex flex-col justify-center pt-3 mt-auto border-t border-border h-[88px]">

                    <p class="text-lg font-extrabold transition-all duration-300 ease-out transform text-foreground group-hover:-translate-y-8 group-hover:opacity-0">
                        ৳ {{ number_format($book->display_price, 0) }}
                    </p>

                    <form action="{{ route('cart.add', $book->id) }}" method="POST" class="absolute transition-all duration-300 ease-out translate-y-8 opacity-0 left-4 right-4 bottom-4 group-hover:translate-y-0 group-hover:opacity-100">
                        @csrf
                        <button type="submit" class="w-full py-2.5 text-sm font-bold text-white transition-all bg-gray-900 shadow-md rounded-lg hover:bg-gray-800 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0">
                            Add to Cart
                        </button>
                    </form>

                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8 text-center md:hidden">
            <a href="{{ route('bestsellers.index') }}" class="inline-block px-8 py-3 text-sm font-bold text-gray-700 transition-colors border-2 border-gray-200 rounded-full hover:bg-gray-100">See All Bestsellers</a>
        </div>
    </div>

    <x-testimonials />

</div>
@endsection