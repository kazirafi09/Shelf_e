@extends('layouts.app')

@section('content')
<div class="container px-4 py-8 mx-auto max-w-7xl" x-data="{ mounted: false }" x-init="setTimeout(() => mounted = true, 50)">
    
    <div class="mb-6 transition-all duration-700 ease-out transform md:mb-8"
         :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-4'">
        <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 md:text-3xl">My Wishlist</h1>
        <p class="mt-1 text-sm font-medium text-gray-500">Manage your saved books, <span class="text-cyan-700">{{ auth()->user()->name }}</span>!</p>
    </div>

    <div class="flex flex-col gap-6 md:flex-row md:gap-8">
        
        <aside class="w-full transition-all duration-700 ease-out delay-100 transform md:w-64 shrink-0"
               :class="mounted ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4'">
            <nav class="flex overflow-x-auto gap-2 pb-2 md:pb-0 md:flex-col md:space-y-2 md:gap-0 snap-x snap-mandatory [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                
                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 font-medium rounded-xl text-gray-600 hover:text-cyan-700 hover:bg-gray-50 shrink-0 snap-start transition-all duration-300 hover:-translate-y-0.5 border border-transparent hover:border-gray-200">
                    <svg class="w-5 h-5 mr-2 text-gray-400 md:mr-3 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Orders & Stats
                </a>

                <a href="{{ route('wishlist.index') }}" class="flex items-center px-4 py-3 font-medium transition-all duration-300 border shadow-sm rounded-xl text-cyan-700 bg-cyan-50 shrink-0 snap-start border-cyan-100">
                    <svg class="w-5 h-5 mr-2 md:mr-3 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    My Wishlist
                </a>
                
                <a href="#" class="flex items-center px-4 py-3 font-medium rounded-xl text-gray-600 hover:text-cyan-700 hover:bg-gray-50 shrink-0 snap-start transition-all duration-300 hover:-translate-y-0.5 border border-transparent hover:border-gray-200">
                    <svg class="w-5 h-5 mr-2 text-gray-400 md:mr-3 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Saved Addresses
                </a>

                <a href="#" class="flex items-center px-4 py-3 font-medium rounded-xl text-gray-600 hover:text-cyan-700 hover:bg-gray-50 shrink-0 snap-start transition-all duration-300 hover:-translate-y-0.5 border border-transparent hover:border-gray-200">
                    <svg class="w-5 h-5 mr-2 text-gray-400 md:mr-3 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Account Settings
                </a>
            </nav>
        </aside>

        <div class="flex-1 min-w-0 transition-all duration-700 ease-out delay-200 transform"
             :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
            
            @if(session('success'))
                <div class="p-4 mb-6 text-sm font-medium text-green-700 border border-green-200 bg-green-50 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            @if($wishlists->count() > 0)
                <div class="grid grid-cols-2 gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 md:gap-6">
                    @foreach($wishlists as $wishlist)
                        @php $book = $wishlist->product; @endphp
                        
                        @if($book) 
                        <div class="relative flex flex-col p-4 overflow-hidden transition-all duration-500 bg-white border border-gray-100 rounded-2xl group hover:shadow-xl hover:-translate-y-2 hover:border-cyan-100">
                            
                            <div class="absolute z-20 top-3 right-3">
                                <form action="{{ route('wishlist.toggle', $book->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="p-2 text-red-500 transition bg-white rounded-full shadow-sm hover:scale-110 hover:text-red-700" title="Remove from Wishlist">
                                        <svg class="w-5 h-5 fill-current" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                    </button>
                                </form>
                            </div>

                            <a href="/product/{{ $book->slug }}" class="z-10 block grow">
                                <div class="relative flex items-center justify-center w-full mb-4 overflow-hidden text-gray-400 bg-gray-100 shadow-sm aspect-[2/3] rounded-lg">
                                    <div class="absolute inset-0 z-10 transition-opacity duration-300 pointer-events-none bg-black/0 group-hover:bg-black/5"></div>
                                    @if($book->image_path)
                                        <img src="{{ asset('storage/' . $book->image_path) }}" alt="{{ $book->title }}" loading="lazy" class="object-cover w-full h-full transition-transform duration-700 ease-out group-hover:scale-110">
                                    @else
                                        <span class="text-xs font-medium tracking-widest uppercase">Cover</span>
                                    @endif
                                </div>
                                
                                <div class="flex items-center mb-1.5 space-x-1">
                                    <svg class="w-4 h-4 text-yellow-400 drop-shadow-sm" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    <span class="text-xs font-bold text-gray-700">{{ $book->rating ?? '0.0' }}</span>
                                </div>
                                
                                <h3 class="font-bold text-gray-900 truncate transition-colors duration-300 group-hover:text-cyan-600" title="{{ $book->title }}">{{ $book->title }}</h3>
                                <p class="mb-3 text-xs font-medium text-gray-500 truncate">{{ $book->author }}</p>
                            </a>
                            
                            <div class="z-10 flex flex-col justify-center pt-3 mt-auto border-t border-gray-100 h-[88px]">
                                <p class="text-lg font-extrabold text-gray-900 transition-all duration-300 ease-out transform group-hover:-translate-y-8 group-hover:opacity-0">
                                    ৳ {{ number_format($book->display_price, 0) }}
                                </p>
                                
                                <form action="{{ route('cart.add', $book->id) }}" method="POST" class="absolute transition-all duration-300 ease-out translate-y-8 opacity-0 left-4 right-4 bottom-4 group-hover:translate-y-0 group-hover:opacity-100">
                                    @csrf
                                    <button type="submit" class="w-full py-2.5 text-sm font-bold text-white transition-all bg-orange-500 shadow-md rounded-lg hover:bg-orange-600 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0">
                                        Add to Cart
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="py-16 text-center bg-white border border-gray-100 shadow-sm rounded-3xl">
                    <div class="inline-flex items-center justify-center w-20 h-20 mb-6 text-pink-500 rounded-full bg-pink-50">
                        <svg class="w-10 h-10 fill-none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </div>
                    <h2 class="mb-2 text-2xl font-bold text-gray-900">Your wishlist is empty</h2>
                    <p class="mb-6 text-gray-500">Save items you love so you don't lose sight of them.</p>
                    <a href="/categories" class="px-6 py-3 font-bold text-white transition-colors bg-cyan-600 rounded-xl hover:bg-cyan-700">Explore Books</a>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection