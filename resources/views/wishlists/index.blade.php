@extends('layouts.dashboard')

@section('page-title', 'My Wishlist')

@section('dashboard-content')

@if(session('success'))
    <div class="p-4 text-sm font-medium text-green-700 border border-green-200 bg-green-50 rounded-xl">
        {{ session('success') }}
    </div>
@endif

@if($wishlists->count() > 0)
    <div class="grid grid-cols-2 gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 md:gap-6">
        @foreach($wishlists as $wishlist)
            @php $book = $wishlist->product; @endphp

            @if($book)
            <div class="relative flex flex-col p-4 overflow-hidden transition-all duration-500 bg-card text-card-foreground border border-border rounded-2xl group hover:shadow-xl hover:-translate-y-2 hover:border-cyan-100">

                <div class="absolute z-20 top-3 right-3">
                    <form action="{{ route('wishlist.toggle', $book->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="p-2 text-red-500 transition bg-background rounded-full shadow-sm hover:scale-110 hover:text-red-700" title="Remove from Wishlist">
                            <svg class="w-5 h-5 fill-current" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        </button>
                    </form>
                </div>

                <a href="/product/{{ $book->slug }}" class="z-10 block grow">
                    <div class="relative flex items-center justify-center w-full mb-4 overflow-hidden text-muted-foreground bg-muted shadow-sm aspect-[2/3] rounded-lg">
                        <div class="absolute inset-0 z-10 transition-opacity duration-300 pointer-events-none bg-black/0 group-hover:bg-black/5"></div>
                        @if($book->image_path)
                            <img src="{{ asset('storage/' . $book->image_path) }}" alt="{{ $book->title }}" loading="lazy" class="object-cover w-full h-full transition-transform duration-700 ease-out group-hover:scale-110">
                        @else
                            <span class="text-xs font-medium tracking-widest uppercase">Cover</span>
                        @endif
                    </div>

                    <div class="flex items-center mb-1.5 space-x-1">
                        <svg class="w-4 h-4 text-yellow-400 drop-shadow-sm" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <span class="text-xs font-bold text-foreground">{{ $book->rating ?? '0.0' }}</span>
                    </div>

                    <h3 class="font-bold text-foreground truncate transition-colors duration-300 group-hover:text-cyan-600" title="{{ $book->title }}">{{ $book->title }}</h3>
                    <p class="mb-3 text-xs font-medium text-muted-foreground truncate">{{ $book->author }}</p>
                </a>

                <div class="z-10 flex flex-col justify-center pt-3 mt-auto border-t border-border h-[88px]">
                    <p class="text-lg font-extrabold text-foreground transition-all duration-300 ease-out transform group-hover:-translate-y-8 group-hover:opacity-0">
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
    <div class="py-16 text-center bg-card text-card-foreground border border-border shadow-sm rounded-3xl">
        <div class="inline-flex items-center justify-center w-20 h-20 mb-6 text-pink-500 rounded-full bg-pink-50">
            <svg class="w-10 h-10 fill-none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
        </div>
        <h2 class="mb-2 text-2xl font-bold text-foreground">Your wishlist is empty</h2>
        <p class="mb-6 text-muted-foreground">Save items you love so you don't lose sight of them.</p>
        <a href="/categories" class="px-6 py-3 font-bold text-white transition-colors bg-cyan-600 rounded-xl hover:bg-cyan-700">Explore Books</a>
    </div>
@endif

@endsection
