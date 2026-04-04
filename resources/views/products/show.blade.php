@extends('layouts.app')

@section('content')
<div class="container px-4 py-8 mx-auto">
    
    <div class="mb-8 text-sm text-muted-foreground">
        <a href="/" class="transition hover:text-orange-500">Home</a> <span class="mx-2">></span>
        <a href="{{ route('categories.index') }}" class="transition hover:text-orange-500">Categories</a> <span class="mx-2">></span>
        <span class="text-foreground">{{ $product->title }}</span>
    </div>

    <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
        
        <div class="lg:col-span-3" x-data="{ openPeekInside: false, currentIndex: 0, total: {{ $product->previews->count() }} }">
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
            
            <div class="w-full aspect-[2/3] bg-muted rounded-2xl flex items-center justify-center text-muted-foreground mb-4 shadow-xl overflow-hidden border border-border">
                @if($product->image_path)
                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->title }}" class="object-cover w-full h-full transition-transform duration-500 hover:scale-105">
                @else
                    Cover Image
                @endif
            </div>

            @if($product->previews->isNotEmpty())
                <button
                    @click="openPeekInside = true; currentIndex = 0"
                    class="flex items-center justify-center w-full gap-2 py-2.5 text-sm font-bold text-cyan-700 bg-cyan-50 hover:bg-cyan-100 border border-cyan-200 rounded-xl transition-colors active:scale-95 mb-4"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Peek Inside
                    <span class="px-1.5 py-0.5 text-xs bg-cyan-100 rounded-md">{{ $product->previews->count() }}</span>
                </button>

                {{-- ======================================================
                     Peek Inside Lightbox
                     Fixed overlay — position takes it out of normal flow
                     ====================================================== --}}
                <div
                    x-show="openPeekInside"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    @keydown.escape.window="openPeekInside = false"
                    @click.self="openPeekInside = false"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4"
                    style="display: none;"
                >
                    {{-- Modal panel --}}
                    <div
                        x-show="openPeekInside"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="relative flex flex-col items-center w-full max-w-3xl max-h-[90vh]"
                    >
                        {{-- Close button --}}
                        <button
                            @click="openPeekInside = false"
                            class="absolute -top-10 right-0 flex items-center justify-center w-9 h-9 text-white rounded-full bg-white/10 hover:bg-white/25 transition-colors"
                            aria-label="Close"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                        {{-- Media slides --}}
                        <div class="relative w-full overflow-hidden rounded-2xl bg-black shadow-2xl">
                            @foreach($product->previews as $preview)
                            <div
                                x-show="currentIndex === {{ $loop->index }}"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="flex items-center justify-center w-full"
                                style="display: none;"
                            >
                                @if($preview->type === 'image')
                                    <img
                                        src="{{ asset('storage/' . $preview->path) }}"
                                        alt="Preview {{ $loop->iteration }}"
                                        class="object-contain w-full max-h-[75vh] rounded-2xl"
                                    >
                                @else
                                    <video
                                        src="{{ asset('storage/' . $preview->path) }}"
                                        controls
                                        class="w-full max-h-[75vh] rounded-2xl"
                                    ></video>
                                @endif
                            </div>
                            @endforeach
                        </div>

                        {{-- Navigation + counter --}}
                        @if($product->previews->count() > 1)
                        <div class="flex items-center justify-between w-full mt-4 px-1">
                            <button
                                @click="currentIndex = (currentIndex - 1 + total) % total"
                                class="flex items-center justify-center w-10 h-10 text-white rounded-full bg-white/10 hover:bg-white/25 transition-colors"
                                aria-label="Previous"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>

                            <span class="text-sm font-semibold text-white/70">
                                <span x-text="currentIndex + 1"></span>
                                <span class="text-white/40">/</span>
                                {{ $product->previews->count() }}
                            </span>

                            <button
                                @click="currentIndex = (currentIndex + 1) % total"
                                class="flex items-center justify-center w-10 h-10 text-white rounded-full bg-white/10 hover:bg-white/25 transition-colors"
                                aria-label="Next"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Dot indicators --}}
                        <div class="flex items-center gap-1.5 mt-3">
                            @foreach($product->previews as $preview)
                            <button
                                @click="currentIndex = {{ $loop->index }}"
                                :class="currentIndex === {{ $loop->index }} ? 'bg-white w-4' : 'bg-white/30 w-2'"
                                class="h-2 rounded-full transition-all duration-200"
                                aria-label="Go to slide {{ $loop->iteration }}"
                            ></button>
                            @endforeach
                        </div>
                        @endif

                    </div>
                </div>
            @endif

            </div>

        <div class="lg:col-span-6">
            <h1 class="mb-2 text-4xl font-bold text-foreground">{{ $product->title }}</h1>

            <div class="flex items-center mb-6 space-x-4">
                <div class="flex items-center text-muted-foreground">
                    <div class="w-8 h-8 mr-3 overflow-hidden bg-muted rounded-full"></div>
                    <span>By <span class="font-bold text-foreground">{{ $product->author }}</span></span>
                </div>
            </div>

            <div class="flex items-center mb-8 space-x-2">
                <span class="font-bold text-foreground">{{ number_format($product->rating, 1) }}</span>
                <div class="flex text-yellow-400">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                </div>
                <span class="text-sm text-muted-foreground">({{ number_format($product->reviews_count ?? 0) }} ratings)</span>
            </div>

            <div class="grid grid-cols-2 pb-10 mb-10 text-sm border-b border-border gap-y-4 gap-x-8">
                <div class="grid grid-cols-2"><span class="font-medium text-foreground">Publisher</span><span class="text-muted-foreground">{{ $product->publisher ?? 'Shelf-E Press' }}</span></div>
                <div class="grid grid-cols-2"><span class="font-medium text-foreground">Language</span><span class="text-muted-foreground">{{ $product->language ?? 'English' }}</span></div>
                <div class="grid grid-cols-2"><span class="font-medium text-foreground">First Publish</span><span class="text-muted-foreground">{{ $product->publish_year ?? 'N/A' }}</span></div>
                <div class="grid grid-cols-2"><span class="font-medium text-foreground">Pages</span><span class="text-muted-foreground">{{ $product->pages ?? 'N/A' }}p</span></div>
                <div class="grid grid-cols-2"><span class="font-medium text-foreground">ISBN</span><span class="text-muted-foreground">{{ $product->isbn ?? 'N/A' }}</span></div>
                <div class="grid grid-cols-2"><span class="font-medium text-foreground">Dimensions</span><span class="text-muted-foreground">{{ $product->dimensions ?? 'N/A' }}</span></div>
            </div>

            <div x-data="{ activeTab: 'description' }">
                <div class="flex mb-6 space-x-8 border-b border-border">
                    <button @click="activeTab = 'description'" :class="activeTab === 'description' ? 'border-cyan-500 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-900'" class="pb-3 font-medium transition border-b-2">Description</button>
                    <button @click="activeTab = 'author'" :class="activeTab === 'author' ? 'border-cyan-500 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-900'" class="pb-3 font-medium transition border-b-2">About Author</button>
                    <button @click="activeTab = 'reviews'" :class="activeTab === 'reviews' ? 'border-cyan-500 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-900'" class="pb-3 font-medium transition border-b-2">Reviews</button>
                </div>

                <div class="leading-relaxed prose text-foreground max-w-none">
                    <div x-show="activeTab === 'description'" x-cloak x-transition.opacity>
                        <p>{{ $product->description }}</p>
                        <h3 class="mt-6 mb-2 font-bold text-foreground">Synopsis</h3>
                        <p>{{ $product->synopsis }}</p>
                    </div>
                    <div x-show="activeTab === 'author'" x-cloak x-transition.opacity>
                        <p>Learn more about {{ $product->author }}...</p>
                    </div>
                    <div x-show="activeTab === 'reviews'" x-cloak x-transition.opacity>
                        <p class="text-sm text-gray-500">See the <a href="#customer-reviews" class="text-cyan-600 hover:underline font-medium">Customer Reviews</a> section below.</p>
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
                <div class="p-8 mb-10 bg-card text-card-foreground border border-border shadow-2xl rounded-3xl">
                    
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
                            <span class="block text-sm text-muted-foreground">Paperback</span>
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
                            <span class="block text-sm text-muted-foreground">Hardcover</span>
                            <template x-if="hasHardcover">
                                <span class="font-bold text-gray-900" x-text="'৳ ' + formatPrice(hardcoverPrice)"></span>
                            </template>
                            <template x-if="!hasHardcover">
                                <span class="text-sm font-medium text-gray-400">Unavailable</span>
                            </template>
                        </button>
                    </div>

                    <div class="flex items-center justify-center mb-8">
                        <div class="flex items-center bg-background border border-border rounded-lg shadow-sm">
                            <button @click="if(quantity > 1) quantity--" type="button" class="px-5 py-2 text-muted-foreground transition rounded-l-lg hover:text-orange-500 hover:bg-muted">-</button>
                            <span class="px-6 py-2 font-medium text-foreground border-border border-x" x-text="quantity"></span>
                            <button @click="quantity++" type="button" class="px-5 py-2 text-muted-foreground transition rounded-r-lg hover:text-orange-500 hover:bg-muted">+</button>
                        </div>
                    </div>

                    <div class="mb-8 text-4xl font-extrabold text-center text-foreground transition-all duration-300" x-text="'৳ ' + formatPrice(totalPrice)">
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

    {{-- ============================================================ --}}
    {{-- Customer Reviews                                             --}}
    {{-- ============================================================ --}}
    <section id="customer-reviews" class="mt-16 pt-10 border-t border-border">

        <div
            x-data="{ showReviewForm: false }"
            class="max-w-3xl"
        >
            {{-- Section header --}}
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-foreground">Customer Reviews</h2>
                    <p class="mt-0.5 text-sm text-muted-foreground">
                        {{ $product->approvedReviews->count() }}
                        {{ Str::plural('review', $product->approvedReviews->count()) }}
                    </p>
                </div>

                @auth
                    <button
                        @click="showReviewForm = !showReviewForm"
                        :class="showReviewForm
                            ? 'bg-gray-100 text-gray-700'
                            : 'bg-cyan-600 text-white hover:bg-cyan-700 shadow-lg shadow-cyan-600/20'"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold rounded-xl transition-all active:scale-95"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a2 2 0 01-1.414.586H9v-2.414A2 2 0 019.586 13z"/>
                        </svg>
                        <span x-text="showReviewForm ? 'Cancel' : 'Write a Review'"></span>
                    </button>
                @else
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-cyan-600 bg-cyan-50 hover:bg-cyan-100 border border-cyan-200 rounded-xl transition-colors">
                        Log in to review
                    </a>
                @endauth
            </div>

            {{-- Write a Review Form --}}
            @auth
            <div
                x-show="showReviewForm"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="mb-10 p-6 bg-card text-card-foreground border border-border rounded-2xl shadow-sm ring-1 ring-gray-900/5"
                style="display: none;"
            >
                <h3 class="mb-5 text-base font-bold text-foreground">Your Review</h3>

                <form action="{{ route('reviews.store', $product) }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block mb-1 text-sm font-semibold text-foreground">Rating</label>
                        <select name="rating" required
                                class="block w-40 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm">
                            <option value="">Pick a rating…</option>
                            <option value="5">★★★★★ — Excellent</option>
                            <option value="4">★★★★☆ — Good</option>
                            <option value="3">★★★☆☆ — Average</option>
                            <option value="2">★★☆☆☆ — Poor</option>
                            <option value="1">★☆☆☆☆ — Terrible</option>
                        </select>
                        @error('rating')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-semibold text-foreground">
                            Title <span class="font-normal text-muted-foreground">(optional)</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title') }}"
                               placeholder="Summarise your experience…"
                               class="block w-full text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm">
                        @error('title')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-semibold text-foreground">Review</label>
                        <textarea name="body" rows="4" required
                                  placeholder="What did you think of this book?"
                                  class="block w-full text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm">{{ old('body') }}</textarea>
                        @error('body')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="showReviewForm = false"
                                class="text-sm font-semibold text-muted-foreground hover:text-foreground transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-6 py-2.5 text-sm font-bold text-white bg-cyan-600 hover:bg-cyan-700 rounded-xl transition-colors active:scale-95">
                            Submit Review
                        </button>
                    </div>
                </form>
            </div>
            @endauth

            {{-- Success flash --}}
            @if(session('success'))
                <div class="mb-6 p-4 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Reviews list --}}
            @if($product->approvedReviews->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center bg-card text-card-foreground border border-border rounded-2xl ring-1 ring-gray-900/5">
                    <svg class="w-12 h-12 mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <p class="font-semibold text-foreground">Be the first to review this book!</p>
                    <p class="mt-1 text-sm text-muted-foreground">Share your thoughts with other readers.</p>
                </div>
            @else
                <div class="space-y-5">
                    @foreach($product->approvedReviews as $review)
                    <article class="p-6 bg-card text-card-foreground border border-border rounded-2xl shadow-sm ring-1 ring-gray-900/5">

                        {{-- Stars + meta --}}
                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            <div class="flex items-center gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200' }}"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>

                            @if($review->is_verified_purchase)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Verified Purchase
                                </span>
                            @endif

                            <span class="ml-auto text-xs text-muted-foreground">
                                {{ $review->user?->name ?? 'Anonymous' }} &middot; {{ $review->created_at->format('M d, Y') }}
                            </span>
                        </div>

                        {{-- Title + body --}}
                        @if($review->title)
                            <p class="mb-1 text-sm font-bold text-foreground">{{ $review->title }}</p>
                        @endif
                        <p class="text-sm leading-relaxed text-muted-foreground">{{ $review->body }}</p>

                    </article>
                    @endforeach
                </div>
            @endif

        </div>
    </section>

</div>
@endsection