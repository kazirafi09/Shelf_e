@extends('layouts.app')

@section('content')
<div class="container px-4 py-8 mx-auto" x-data="{ mobileFiltersOpen: false }">
    
    <div class="mb-6 text-sm text-gray-500">
        <a href="/" class="transition-colors hover:text-orange-500">Home</a> 
        <span class="mx-2">&gt;</span> 
        
        {{-- If the title is anything other than 'All Books', we show the expanded breadcrumb --}}
        @if($pageTitle !== 'All Books')
            <a href="/categories" class="transition-colors hover:text-orange-500">Books</a>
            <span class="mx-2">&gt;</span>
            <span class="font-medium text-gray-900">{{ $pageTitle }}</span>
        @else
            <span class="font-medium text-gray-900">Books</span>
        @endif
    </div>

    <div class="mb-8">
        {{-- Clean header without inline buttons --}}
        <h1 class="text-3xl font-bold text-gray-900">
            {{ $pageTitle ?? 'All Books' }}
        </h1>
    </div>

    {{-- NEW: Floating Mobile Filter Button --}}
    <button @click="mobileFiltersOpen = true" 
            class="fixed z-40 flex items-center px-6 py-3.5 text-sm font-bold text-white transition-transform transform -translate-x-1/2 bg-gray-900 rounded-full shadow-2xl bottom-8 left-1/2 lg:hidden active:scale-95">
        <svg class="w-5 h-5 mr-2 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
        Filters
    </button>

    <div class="flex items-center justify-between mb-8">      
        <button @click="mobileFiltersOpen = true" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md lg:hidden hover:bg-gray-50">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            Filters
        </button>
    </div>

    <div class="relative flex flex-col gap-8 lg:flex-row">
        
        <div x-show="mobileFiltersOpen" 
             x-transition.opacity.duration.300ms
             @click="mobileFiltersOpen = false"
             class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden" style="display: none;"></div>

        <aside class="fixed inset-y-0 left-0 z-50 w-3/4 h-full max-w-sm p-6 pb-24 overflow-y-auto transition-transform duration-300 ease-in-out transform bg-white shadow-2xl lg:sticky lg:top-36 lg:translate-x-0 lg:w-72 lg:p-6 lg:rounded-2xl lg:border lg:border-gray-100 lg:shadow-sm lg:max-h-[calc(100vh-10rem)] shrink-0 custom-scrollbar"
            :class="mobileFiltersOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <div class="flex items-center justify-between mb-6 lg:hidden">
                <h2 class="text-xl font-bold text-gray-900">Filters</h2>
                <button @click="mobileFiltersOpen = false" class="p-2 text-gray-400 bg-gray-100 rounded-full hover:text-gray-600 hover:bg-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="/categories" method="GET">
                <div class="pb-4 mb-4 border-b border-gray-200" x-data="{ open: true }">
                    <button type="button" @click="open = !open" class="flex items-center justify-between w-full font-bold text-left text-gray-900 focus:outline-none">
                        Genres
                        <svg class="w-5 h-5 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-4 space-y-3">
                        @foreach($genres as $genre)
                            <label class="flex items-center space-x-3 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="genres[]" value="{{ $genre->id }}" 
                                    class="w-4 h-4 border-gray-300 rounded text-cyan-500 focus:ring-cyan-500"
                                    @if(is_array(request('genres')) && in_array($genre->id, request('genres'))) checked @endif>
                                <span class="capitalize">{{ $genre->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="py-4 mb-6 border-b border-gray-200" x-data="{ open: true }">
                    <button type="button" @click="open = !open" class="flex items-center justify-between w-full font-bold text-left text-gray-900 focus:outline-none">
                        Price Range
                        <svg class="w-5 h-5 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-collapse class="flex items-center justify-between mt-4 space-x-4 text-sm">
                        <div class="flex flex-col w-1/2">
                            <span class="mb-1 text-gray-500">Min</span>
                            <div class="flex items-center px-3 py-2 border border-gray-200 rounded-md bg-gray-50">
                                <span class="mr-1 text-gray-500">৳</span>
                                <input type="number" name="min_price" value="{{ request('min_price', 100) }}" class="w-full p-0 text-sm bg-transparent border-none focus:ring-0">
                            </div>
                        </div>
                        <span class="mt-5 text-gray-400">-</span>
                        <div class="flex flex-col w-1/2">
                            <span class="mb-1 text-gray-500">Max</span>
                            <div class="flex items-center px-3 py-2 border border-gray-200 rounded-md bg-gray-50">
                                <span class="mr-1 text-gray-500">৳</span>
                                <input type="number" name="max_price" value="{{ request('max_price', 3000) }}" class="w-full p-0 text-sm bg-transparent border-none focus:ring-0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pb-4 mb-4 border-b border-gray-200" x-data="{ open: false }">
                    <button type="button" @click="open = !open" class="flex items-center justify-between w-full font-bold text-left text-gray-900 focus:outline-none">
                        Authors
                        <svg class="w-5 h-5 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-4 space-y-3 overflow-y-auto max-h-48 custom-scrollbar">
                        @foreach($authors as $author)
                            <label class="flex items-center space-x-3 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="authors[]" value="{{ $author }}" 
                                    class="w-4 h-4 border-gray-300 rounded text-cyan-500 focus:ring-cyan-500"
                                    @if(is_array(request('authors')) && in_array($author, request('authors'))) checked @endif>
                                <span class="capitalize">{{ $author }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="pb-4 mb-4 border-b border-gray-200" x-data="{ open: true }">
                    <button type="button" @click="open = !open" class="flex items-center justify-between w-full font-bold text-left text-gray-900 focus:outline-none">
                        Rating
                        <svg class="w-5 h-5 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-4 space-y-3">
                        @foreach([4, 3, 2, 1] as $rating)
                            <label class="flex items-center space-x-3 text-sm text-gray-700 cursor-pointer">
                                <input type="radio" name="min_rating" value="{{ $rating }}" 
                                    class="w-4 h-4 border-gray-300 text-cyan-500 focus:ring-cyan-500"
                                    @if(request('min_rating') == $rating) checked @endif>
                                <span class="flex items-center">
                                    @for($i = 0; $i < $rating; $i++)
                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    @endfor
                                    <span class="ml-1 text-gray-500">& Up</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="flex flex-col space-y-3">
                    <button type="submit" class="w-full py-3 font-medium text-white transition bg-orange-500 rounded-md hover:bg-orange-600">Apply Changes</button>
                    <a href="/categories" class="w-full py-3 font-medium text-center text-orange-500 transition bg-white border border-orange-500 rounded-md hover:bg-orange-50">Clear Filters</a>
                </div>


            </form>
        </aside>

        <div class="flex-1">
            <div class="flex items-center justify-between p-4 mb-6 bg-white border border-gray-200 rounded-lg">
                <div class="text-sm font-medium text-gray-500">
                    Showing all <span class="font-bold text-gray-900">{{ $products->count() }}</span> results
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                @forelse ($products as $product)
                    <div class="relative flex flex-col p-3 transition duration-300 border border-gray-100 bg-gray-50 rounded-xl group hover:shadow-lg">
                        <button class="absolute z-10 text-gray-400 top-6 right-6 hover:text-orange-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        </button>
                        
                        <a href="/product/{{ $product->slug }}" class="block grow">
                            <div class="w-full aspect-[2/3] bg-gray-200 rounded-md mb-3 flex items-center justify-center text-gray-400 overflow-hidden">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->title }}" class="object-cover w-full h-full">
                                @else
                                    Cover Image
                                @endif
                            </div>
                            
                            <div class="flex items-center justify-center mb-1 space-x-1">
                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                <span class="text-sm font-bold text-gray-700">{{ $product->rating }}</span>
                            </div>
                            
                            <h3 class="font-bold text-gray-900 truncate transition group-hover:text-cyan-500" title="{{ $product->title }}">{{ $product->title }}</h3>
                            <p class="mb-2 text-xs text-gray-500 truncate">{{ $product->author }}</p>
                        </a>
                        
                        <div class="pt-2 mt-auto">
                            <p class="mb-3 text-lg font-bold text-gray-900">৳ {{ number_format($product->price, 0) }}</p>
                            
                            {{-- THE FIX: Wrap the button in a secure POST form --}}
                            <form action="{{ route('cart.add', $product->id) }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" class="w-full py-2 font-bold text-white transition-colors bg-orange-500 rounded-md hover:bg-orange-600 active:scale-[0.98]">
                                    Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-gray-500 col-span-full">
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