@extends('layouts.app')

@section('content')
<div class="container px-4 py-8 mx-auto">
    
    <div class="mb-8 text-sm text-gray-500">
        <a href="/" class="hover:text-orange-500">Home</a> <span class="mx-2">></span> 
        <a href="/categories" class="hover:text-orange-500">Categories</a> <span class="mx-2">></span> 
        <span class="text-gray-900">{{ $product->title }}</span>
    </div>

    <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
        
        <div class="lg:col-span-3">
            <button class="flex items-center mb-4 text-gray-500 transition hover:text-orange-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                Add to Wishlist
            </button>
            
            <div class="w-full aspect-[2/3] bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 mb-4 shadow-md overflow-hidden">
                @if($product->image_path)
                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->title }}" class="object-cover w-full h-full">
                @else
                    Cover Image
                @endif
            </div>
            
            <div class="text-center">
                <a href="#" class="font-medium transition text-cyan-500 hover:text-cyan-600">Peek Inside</a>
            </div>
        </div>

        <div class="lg:col-span-6">
            <h1 class="mb-2 text-4xl font-bold text-gray-900">{{ $product->title }}</h1>
            
            <div class="flex items-center mb-6 space-x-4">
                <div class="flex items-center text-gray-600">
                    <div class="w-8 h-8 mr-3 overflow-hidden bg-gray-300 rounded-full"></div>
                    <span>By <span class="font-bold text-gray-900">{{ $product->author }}</span></span>
                </div>
            </div>

            <div class="flex items-center mb-8 space-x-2">
                <span class="font-bold text-gray-900">{{ $product->rating }}</span>
                <div class="flex text-yellow-400">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                </div>
                <span class="text-sm text-gray-500">(121,640 ratings)</span>
            </div>

            <div class="grid grid-cols-2 pb-10 mb-10 text-sm border-b border-gray-200 gap-y-3 gap-x-8">
                <div class="grid grid-cols-2">
                    <span class="font-medium text-gray-900">Publisher</span>
                    <span class="text-gray-600">Shelf-E Press</span>
                </div>
                <div class="grid grid-cols-2">
                    <span class="font-medium text-gray-900">Language</span>
                    <span class="text-gray-600">English</span>
                </div>
                <div class="grid grid-cols-2">
                    <span class="font-medium text-gray-900">First Publish</span>
                    <span class="text-gray-600">2026</span>
                </div>
                <div class="grid grid-cols-2">
                    <span class="font-medium text-gray-900">Pages</span>
                    <span class="text-gray-600">320p</span>
                </div>
                <div class="grid grid-cols-2">
                    <span class="font-medium text-gray-900">ISBN</span>
                    <span class="text-gray-600">978-123456789</span>
                </div>
                <div class="grid grid-cols-2">
                    <span class="font-medium text-gray-900">Dimensions</span>
                    <span class="text-gray-600">5.12 x 8.0 inches</span>
                </div>
            </div>

            <div class="flex mb-6 space-x-8 border-b border-gray-200">
                <button class="pb-3 font-medium border-b-2 text-cyan-600 border-cyan-500">Description</button>
                <button class="pb-3 font-medium text-gray-500 transition hover:text-gray-900">About Author</button>
                <button class="pb-3 font-medium text-gray-500 transition hover:text-gray-900">Reviews</button>
            </div>

            <div class="leading-relaxed prose text-gray-700 max-w-none">
                <p>{{ $product->description }}</p>
                <p class="mt-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam in dui mauris. Vivamus hendrerit arcu sed erat molestie vehicula. Sed auctor neque eu tellus rhoncus ut eleifend nibh porttitor. Ut in nulla enim. Phasellus molestie magna non est bibendum non venenatis nisl tempor.</p>
                <h3 class="mt-6 mb-2 font-bold text-gray-900">Synopsis</h3>
                <p>Suspendisse dictum feugiat nisl ut dapibus. Mauris iaculis porttitor posuere. Praesent id metus massa, ut blandit odio. Proin quis tortor orci. Etiam at risus et justo dignissim congue. Donec congue lacinia dui, a porttitor lectus condimentum laoreet.</p>
            </div>
        </div>

        <div class="lg:col-span-3">
            
            <div class="sticky top-6">
                <div class="p-6 mb-10 border border-gray-200 bg-gray-50 rounded-xl">
                    
                    <div class="flex mb-6 space-x-3 text-center">
                        <div class="flex-1 p-2 transition bg-white border border-gray-200 rounded-md cursor-pointer hover:border-cyan-500">
                            <span class="block text-sm text-gray-500">Paperback</span>
                            <span class="font-bold text-gray-900">৳ {{ number_format($product->price * 0.8, 0) }}</span>
                        </div>
                        <div class="flex-1 p-2 bg-white border-2 rounded-md cursor-pointer border-cyan-500">
                            <span class="block text-sm text-gray-500">Hardcover</span>
                            <span class="font-bold text-gray-900">৳ {{ number_format($product->price, 0) }}</span>
                        </div>
                    </div>

                    <p class="mb-2 text-sm text-center text-gray-500">{{ $product->stock_quantity }} in stock</p>
                    <div class="flex items-center justify-center mb-6">
                        <div class="flex items-center bg-white border border-gray-300 rounded-md">
                            <button class="px-4 py-1 text-gray-600 hover:text-orange-500">-</button>
                            <span class="px-4 py-1 font-medium border-gray-300 border-x">1</span>
                            <button class="px-4 py-1 text-gray-600 hover:text-orange-500">+</button>
                        </div>
                    </div>

                    <div class="flex items-center justify-center mb-6">
                        <label class="flex items-center space-x-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" class="w-4 h-4 border-gray-300 rounded text-cyan-500 focus:ring-cyan-500">
                            <span>Gift Wrapping</span>
                        </label>
                    </div>

                    <div class="mb-6 text-3xl font-bold text-center text-gray-900">
                        ৳ {{ number_format($product->price, 0) }}
                    </div>

                    <div class="space-y-3">
                        <button class="w-full py-3 font-medium text-white transition bg-orange-500 rounded-md shadow-sm hover:bg-orange-600">
                            Buy Now
                        </button>
                        
                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                            @csrf <button type="submit" class="w-full py-3 font-medium text-orange-500 transition bg-white border border-orange-500 rounded-md hover:bg-orange-50">
                                Add to cart
                            </button>
                        </form>
                    </div>

                    @if(session('success'))
                        <div class="p-4 mt-4 text-sm text-green-700 bg-green-100 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif
                </div>

                <h3 class="mb-4 text-lg font-bold text-gray-900">You might also like</h3>
                <div class="space-y-4">
                    @foreach($relatedProducts as $related)
                    <a href="/product/{{ $related->slug }}" class="flex items-center group">
                        <div class="flex items-center justify-center w-16 h-24 text-xs text-gray-400 bg-gray-200 rounded-md shrink-0">Cover</div>
                        <div class="ml-4">
                            <h4 class="text-sm font-bold text-gray-900 transition group-hover:text-cyan-500 line-clamp-2">{{ $related->title }}</h4>
                            <div class="flex items-center mt-1 space-x-1 text-xs">
                                <span class="font-bold text-gray-700">{{ $related->rating }}</span>
                                <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection