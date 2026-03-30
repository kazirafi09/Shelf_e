@extends('layouts.app')

@section('content')
<div class="container px-4 py-8 mx-auto">
    
    <div class="mb-8 text-sm text-gray-500">
        <a href="/" class="transition hover:text-orange-500">Home</a> <span class="mx-2">></span> 
        <a href="{{ route('categories.index') }}" class="transition hover:text-orange-500">Categories</a> <span class="mx-2">></span> 
        <span class="text-gray-900">{{ $product->title }}</span>
    </div>

    <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
        
        <div class="lg:col-span-3">
            @auth
                @php
                    $isWishlisted = \App\Models\Wishlist::where('user_id', auth()->id())
                        ->where('product_id', $product->id)
                        ->exists();
                @endphp
                <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST" class="inline-block mb-4">
                    @csrf
                    <button type="submit" class="flex items-center transition {{ $isWishlisted ? 'text-orange-500 font-bold' : 'text-gray-500 hover:text-orange-500' }}">
                        <svg class="w-5 h-5 mr-2 {{ $isWishlisted ? 'fill-current' : 'fill-none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        {{ $isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist' }}
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="flex items-center mb-4 text-gray-500 transition hover:text-orange-500">
                    <svg class="w-5 h-5 mr-2 fill-none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    Log in to Wishlist
                </a>
            @endauth
            
            <div class="w-full aspect-[2/3] bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 mb-4 shadow-xl overflow-hidden border border-gray-100">
                @if($product->image_path)
                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->title }}" class="object-cover w-full h-full transition-transform duration-500 hover:scale-105">
                @else
                    Cover Image
                @endif
            </div>
            
            </div>

        <div class="lg:col-span-6">
            <h1 class="mb-2 text-4xl font-bold text-gray-900">{{ $product->title }}</h1>
            
            <div class="flex items-center mb-6 space-x-4">
                <div class="flex items-center text-gray-600">
                    <div class="w-8 h-8 mr-3 overflow-hidden bg-gray-200 rounded-full"></div>
                    <span>By <span class="font-bold text-gray-900">{{ $product->author }}</span></span>
                </div>
            </div>

            <div class="flex items-center mb-8 space-x-2">
                <span class="font-bold text-gray-900">{{ number_format($product->rating, 1) }}</span>
                <div class="flex text-yellow-400">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                </div>
                <span class="text-sm text-gray-500">({{ number_format($product->reviews_count ?? 0) }} ratings)</span>
            </div>

            <div class="grid grid-cols-2 pb-10 mb-10 text-sm border-b border-gray-100 gap-y-4 gap-x-8">
                <div class="grid grid-cols-2"><span class="font-medium text-gray-900">Publisher</span><span class="text-gray-600">{{ $product->publisher ?? 'Shelf-E Press' }}</span></div>
                <div class="grid grid-cols-2"><span class="font-medium text-gray-900">Language</span><span class="text-gray-600">{{ $product->language ?? 'English' }}</span></div>
                <div class="grid grid-cols-2"><span class="font-medium text-gray-900">First Publish</span><span class="text-gray-600">{{ $product->publish_year ?? 'N/A' }}</span></div>
                <div class="grid grid-cols-2"><span class="font-medium text-gray-900">Pages</span><span class="text-gray-600">{{ $product->pages ?? 'N/A' }}p</span></div>
                <div class="grid grid-cols-2"><span class="font-medium text-gray-900">ISBN</span><span class="text-gray-600">{{ $product->isbn ?? 'N/A' }}</span></div>
                <div class="grid grid-cols-2"><span class="font-medium text-gray-900">Dimensions</span><span class="text-gray-600">{{ $product->dimensions ?? 'N/A' }}</span></div>
            </div>

            <div x-data="{ activeTab: 'description' }">
                <div class="flex mb-6 space-x-8 border-b border-gray-100">
                    <button @click="activeTab = 'description'" :class="activeTab === 'description' ? 'border-cyan-500 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-900'" class="pb-3 font-medium transition border-b-2">Description</button>
                    <button @click="activeTab = 'author'" :class="activeTab === 'author' ? 'border-cyan-500 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-900'" class="pb-3 font-medium transition border-b-2">About Author</button>
                    <button @click="activeTab = 'reviews'" :class="activeTab === 'reviews' ? 'border-cyan-500 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-900'" class="pb-3 font-medium transition border-b-2">Reviews</button>
                </div>

                <div class="leading-relaxed prose text-gray-700 max-w-none">
                    <div x-show="activeTab === 'description'" x-cloak x-transition.opacity>
                        <p>{{ $product->description }}</p>
                        <h3 class="mt-6 mb-2 font-bold text-gray-900">Synopsis</h3>
                        <p>{{ $product->synopsis }}</p>
                    </div>
                    <div x-show="activeTab === 'author'" x-cloak x-transition.opacity>
                        <p>Learn more about {{ $product->author }}...</p>
                    </div>
                    <div x-show="activeTab === 'reviews'" x-cloak x-transition.opacity>
                        <p>Customer reviews will appear here.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-3">
            <div class="sticky top-6" x-data="{
                paperbackPrice: {{ $product->paperback_price ? $product->paperback_price : 'null' }},
                hardcoverPrice: {{ $product->hardcover_price ? $product->hardcover_price : 'null' }},
                quantity: 1,
                actionType: 'add_to_cart',
                loading: false,
                
                // Intelligently set default format based on what is available
                format: '{{ $product->paperback_price ? 'paperback' : ($product->hardcover_price ? 'hardcover' : '') }}',
                
                get hasPaperback() { return this.paperbackPrice !== null; },
                get hasHardcover() { return this.hardcoverPrice !== null; },
                
                get currentPrice() {
                    return this.format === 'paperback' ? this.paperbackPrice : this.hardcoverPrice;
                },
                get totalPrice() {
                    return this.currentPrice * this.quantity;
                },
                formatPrice(price) {
                    if (!price) return '0';
                    return new Intl.NumberFormat('en-IN', { maximumFractionDigits: 0 }).format(price);
                }
            }">
                <div class="p-8 mb-10 bg-white border border-gray-100 shadow-2xl rounded-3xl">
                    
                    <div class="flex mb-8 space-x-3 text-center">
                        <button type="button" 
                            @click="if(hasPaperback) format = 'paperback'" 
                            :disabled="!hasPaperback"
                            :class="{
                                'border-cyan-500 bg-cyan-50 ring-1 ring-cyan-500 cursor-pointer': format === 'paperback' && hasPaperback,
                                'border-gray-200 bg-white hover:border-cyan-300 cursor-pointer': format !== 'paperback' && hasPaperback,
                                'border-gray-100 bg-gray-50 opacity-50 cursor-not-allowed': !hasPaperback
                            }"
                            class="flex-1 p-3 text-left transition border rounded-xl">
                            <span class="block text-sm text-gray-500">Paperback</span>
                            <template x-if="hasPaperback">
                                <span class="font-bold text-gray-900" x-text="'৳ ' + formatPrice(paperbackPrice)"></span>
                            </template>
                            <template x-if="!hasPaperback">
                                <span class="text-sm font-medium text-gray-400">Unavailable</span>
                            </template>
                        </button>

                        <button type="button" 
                            @click="if(hasHardcover) format = 'hardcover'" 
                            :disabled="!hasHardcover"
                            :class="{
                                'border-cyan-500 bg-cyan-50 ring-1 ring-cyan-500 cursor-pointer': format === 'hardcover' && hasHardcover,
                                'border-gray-200 bg-white hover:border-cyan-300 cursor-pointer': format !== 'hardcover' && hasHardcover,
                                'border-gray-100 bg-gray-50 opacity-50 cursor-not-allowed': !hasHardcover
                            }"
                            class="flex-1 p-3 text-left transition border rounded-xl">
                            <span class="block text-sm text-gray-500">Hardcover</span>
                            <template x-if="hasHardcover">
                                <span class="font-bold text-gray-900" x-text="'৳ ' + formatPrice(hardcoverPrice)"></span>
                            </template>
                            <template x-if="!hasHardcover">
                                <span class="text-sm font-medium text-gray-400">Unavailable</span>
                            </template>
                        </button>
                    </div>

                    <div class="flex items-center justify-center mb-8">
                        <div class="flex items-center bg-white border border-gray-200 rounded-lg shadow-sm">
                            <button @click="if(quantity > 1) quantity--" type="button" class="px-5 py-2 text-gray-500 transition rounded-l-lg hover:text-orange-500 hover:bg-gray-50">-</button>
                            <span class="px-6 py-2 font-medium text-gray-900 border-gray-200 border-x" x-text="quantity"></span>
                            <button @click="quantity++" type="button" class="px-5 py-2 text-gray-500 transition rounded-r-lg hover:text-orange-500 hover:bg-gray-50">+</button>
                        </div>
                    </div>

                    <div class="mb-8 text-4xl font-extrabold text-center text-gray-900 transition-all duration-300" x-text="'৳ ' + formatPrice(totalPrice)">
                        ৳ {{ number_format($product->paperback_price ?? $product->hardcover_price ?? 0, 0) }}
                    </div>

                    <form action="{{ route('cart.add', $product->id) }}" method="POST" @submit="loading = true">
                        @csrf
                        <input type="hidden" name="format" x-model="format">
                        <input type="hidden" name="quantity" x-model="quantity">
                        <input type="hidden" name="action_type" x-model="actionType">
                        
                        <div class="space-y-3">
                            <button type="submit" 
                                    @click="actionType = 'buy_now'"
                                    :disabled="loading || (!hasPaperback && !hasHardcover)" 
                                    class="w-full py-4 font-bold text-white transition bg-orange-500 shadow-lg rounded-xl shadow-orange-500/30 hover:bg-orange-600 hover:shadow-orange-600/40 disabled:opacity-50 disabled:cursor-not-allowed">
                                Buy Now
                            </button>
                            
                            <button type="submit" 
                                    @click="actionType = 'add_to_cart'"
                                    :disabled="loading || (!hasPaperback && !hasHardcover)" 
                                    class="w-full py-4 font-bold text-orange-500 transition bg-white border-2 border-orange-100 shadow-sm rounded-xl hover:bg-orange-50 hover:border-orange-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!loading">Add to Cart</span>
                                <span x-show="loading" x-cloak class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-3 -ml-1 text-orange-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Processing...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection