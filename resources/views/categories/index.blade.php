@extends('layouts.app')

@section('content')
<div class="container px-4 py-8 mx-auto" x-data="{ mobileFiltersOpen: false }">
    
    <div class="mb-6 text-sm text-muted-foreground">
        <a href="/" class="transition-colors hover:text-gray-700">Home</a>
        <span class="mx-2">&gt;</span>

        {{-- If the title is anything other than 'All Books', we show the expanded breadcrumb --}}
        @if($pageTitle !== 'All Books')
            <a href="/categories" class="transition-colors hover:text-gray-700">Books</a>
            <span class="mx-2">&gt;</span>
            <span class="font-medium text-foreground">{{ $pageTitle }}</span>
        @else
            <span class="font-medium text-foreground">Books</span>
        @endif
    </div>

    <div class="mb-8">
        {{-- Clean header without inline buttons --}}
        <h1 class="text-3xl font-bold text-foreground">
            {{ $pageTitle ?? 'All Books' }}
        </h1>
    </div>

<div class="flex items-center justify-between mb-8">      
        <button @click="mobileFiltersOpen = true" class="flex items-center px-4 py-2 text-sm font-medium text-foreground bg-background border border-border rounded-md lg:hidden hover:bg-muted">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            Filters
        </button>
    </div>

    <div class="relative flex flex-col gap-8 lg:flex-row">
        
        <div x-show="mobileFiltersOpen" 
             x-transition.opacity.duration.300ms
             @click="mobileFiltersOpen = false"
             class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden" style="display: none;"></div>

        <aside class="fixed inset-y-0 left-0 z-50 w-3/4 h-full max-w-sm p-6 pb-24 overflow-y-auto transition-transform duration-300 ease-in-out transform bg-background shadow-2xl lg:sticky lg:top-36 lg:translate-x-0 lg:w-72 lg:p-6 lg:rounded-2xl lg:border lg:border-border lg:shadow-sm lg:max-h-[calc(100vh-10rem)] shrink-0 custom-scrollbar"
            :class="mobileFiltersOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <div class="flex items-center justify-between mb-6 lg:hidden">
                <h2 class="text-xl font-bold text-foreground">Filters</h2>
                <button @click="mobileFiltersOpen = false" class="p-2 text-muted-foreground bg-muted rounded-full hover:text-foreground hover:bg-muted hover:opacity-80">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="/categories" method="GET">
                <div class="pb-4 mb-4 border-b border-border" x-data="{ open: true }">
                    <button type="button" @click="open = !open" class="flex items-center justify-between w-full font-bold text-left text-foreground focus:outline-none">
                        Genres
                        <svg class="w-5 h-5 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-4 space-y-3">
                        @foreach($genres as $genre)
                            <label class="flex items-center space-x-3 text-sm text-foreground cursor-pointer">
                                <input type="checkbox" name="genres[]" value="{{ $genre->id }}"
                                    class="w-4 h-4 border-border rounded text-gray-500 focus:ring-gray-500"
                                    @if(is_array(request('genres')) && in_array($genre->id, request('genres'))) checked @endif>
                                <span class="capitalize">{{ $genre->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="py-4 mb-6 border-b border-border" x-data="{ open: true }">
                    <button type="button" @click="open = !open" class="flex items-center justify-between w-full font-bold text-left text-foreground focus:outline-none">
                        Price Range
                        <svg class="w-5 h-5 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-collapse class="flex items-center justify-between mt-4 space-x-4 text-sm">
                        <div class="flex flex-col w-1/2">
                            <span class="mb-1 text-muted-foreground">Min</span>
                            <div class="flex items-center px-3 py-2 border border-border rounded-md bg-muted">
                                <span class="mr-1 text-muted-foreground">৳</span>
                                <input type="number" name="min_price" value="{{ request('min_price', 100) }}" class="w-full p-0 text-sm bg-transparent border-none focus:ring-0 text-foreground">
                            </div>
                        </div>
                        <span class="mt-5 text-muted-foreground">-</span>
                        <div class="flex flex-col w-1/2">
                            <span class="mb-1 text-muted-foreground">Max</span>
                            <div class="flex items-center px-3 py-2 border border-border rounded-md bg-muted">
                                <span class="mr-1 text-muted-foreground">৳</span>
                                <input type="number" name="max_price" value="{{ request('max_price', 3000) }}" class="w-full p-0 text-sm bg-transparent border-none focus:ring-0 text-foreground">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pb-4 mb-4 border-b border-border" x-data="{ open: false }">
                    <button type="button" @click="open = !open" class="flex items-center justify-between w-full font-bold text-left text-foreground focus:outline-none">
                        Authors
                        <svg class="w-5 h-5 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-4 space-y-3 overflow-y-auto max-h-48 custom-scrollbar">
                        @foreach($authors as $author)
                            <label class="flex items-center space-x-3 text-sm text-foreground cursor-pointer">
                                <input type="checkbox" name="authors[]" value="{{ $author }}"
                                    class="w-4 h-4 border-border rounded text-gray-500 focus:ring-gray-500"
                                    @if(is_array(request('authors')) && in_array($author, request('authors'))) checked @endif>
                                <span class="capitalize">{{ $author }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="pb-4 mb-4 border-b border-border" x-data="{ open: true }">
                    <button type="button" @click="open = !open" class="flex items-center justify-between w-full font-bold text-left text-foreground focus:outline-none">
                        Rating
                        <svg class="w-5 h-5 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-4 space-y-3">
                        @foreach([4, 3, 2, 1] as $rating)
                            <label class="flex items-center space-x-3 text-sm text-foreground cursor-pointer">
                                <input type="radio" name="min_rating" value="{{ $rating }}"
                                    class="w-4 h-4 border-border text-gray-500 focus:ring-gray-500"
                                    @if(request('min_rating') == $rating) checked @endif>
                                <span class="flex items-center">
                                    @for($i = 0; $i < $rating; $i++)
                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    @endfor
                                    <span class="ml-1 text-muted-foreground">& Up</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="flex flex-col space-y-3">
                    <button type="submit" class="w-full py-3 font-medium text-white transition bg-gray-700 rounded-md hover:bg-gray-800">Apply Changes</button>
                    <a href="/categories" class="w-full py-3 font-medium text-center text-gray-700 transition bg-white border border-gray-700 rounded-md hover:bg-gray-50">Clear Filters</a>
                </div>


            </form>
        </aside>

        <div class="flex-1">
            <div class="flex items-center justify-between p-4 mb-6 bg-card text-card-foreground border border-border rounded-lg">
                <div class="text-sm font-medium text-muted-foreground">
                    Showing <span class="font-bold text-foreground">{{ $products->total() }}</span>
                    @if(isset($isBestsellers) && $isBestsellers) books sorted by most sold @else results @endif
                </div>
                @if(isset($isBestsellers) && $isBestsellers)
                    <div class="flex items-center gap-1.5 text-xs font-semibold text-muted-foreground">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        Most sold first
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                @forelse ($products as $product)
                    @php $rank = $products->firstItem() + $loop->index; @endphp
                    <div class="relative flex flex-col p-3 transition duration-300 border border-border bg-card text-card-foreground rounded-xl group hover:shadow-lg">
                        @if(isset($isBestsellers) && $isBestsellers)
                            <div class="absolute top-2 left-2 z-10 flex items-center justify-center w-7 h-7 rounded-full text-xs font-black
                                {{ $rank === 1 ? 'bg-yellow-400 text-yellow-900' : ($rank === 2 ? 'bg-gray-300 text-gray-700' : ($rank === 3 ? 'bg-amber-600 text-white' : 'bg-gray-100 text-gray-500')) }}">
                                {{ $rank }}
                            </div>
                        @endif
                        @auth
                            @php
                                $isWishlisted = \App\Models\Wishlist::where('user_id', auth()->id())
                                    ->where('product_id', $product->id)
                                    ->exists();
                            @endphp
                            <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST" class="absolute z-10 top-6 right-6">
                                @csrf
                                <button type="submit" class="transition hover:scale-110 {{ $isWishlisted ? 'text-red-500' : 'text-gray-400 hover:text-red-500' }}">
                                    <svg class="w-5 h-5 {{ $isWishlisted ? 'fill-current' : 'fill-none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="absolute z-10 text-gray-400 top-6 right-6 hover:text-red-500" title="Log in to wishlist">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            </a>
                        @endauth
                        
                        <a href="/product/{{ $product->slug }}" class="block grow">
                            <div
                                x-data="{ zoomed: false }"
                                @mouseenter="zoomed = true"
                                @mouseleave="zoomed = false"
                                class="relative w-full aspect-[2/3] bg-muted rounded-md mb-3 flex items-center justify-center text-muted-foreground overflow-hidden"
                            >
                                @if($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->title }}"
                                         class="object-cover w-full h-full transition-transform duration-300"
                                         :class="{ 'scale-125 z-10': zoomed }">
                                @else
                                    Cover Image
                                @endif
                            </div>
                            
                            <div class="flex items-center justify-center mb-1 space-x-1">
                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                <span class="text-sm font-bold text-foreground">{{ number_format($product->approved_reviews_avg_rating ?? 0, 1) }}</span>
                            </div>
                            
                            <h3 class="font-bold text-foreground truncate transition group-hover:text-gray-500" title="{{ $product->title }}">{{ $product->title }}</h3>
                            <p class="mb-2 text-xs text-muted-foreground truncate">{{ $product->author }}</p>
                        </a>
                        
                        <div class="pt-2 mt-auto">
                            @php $activeSalePrice = $product->active_sale_price; @endphp
                            @if($activeSalePrice)
                                <div class="mb-3">
                                    <span class="text-xs line-through text-gray-400">৳ {{ number_format($product->display_price, 0) }}</span>
                                    <span class="ml-1 text-lg font-bold text-foreground">৳ {{ number_format($activeSalePrice, 0) }}</span>
                                </div>
                            @else
                                <p class="mb-3 text-lg font-bold text-foreground">৳ {{ number_format($product->display_price, 0) }}</p>
                            @endif

                            {{-- THE FIX: Wrap the button in a secure POST form --}}
                            <form action="{{ route('cart.add', $product->id) }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" class="w-full py-2 font-bold text-white transition-colors bg-gray-700 rounded-md hover:bg-gray-800 active:scale-[0.98]">
                                    Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-muted-foreground col-span-full">
                        No books found matching your criteria. Try clearing your filters.
                    </div>
                @endforelse
            </div>
            <div class="mt-14">
                {{ $products->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
</div>
@endsection