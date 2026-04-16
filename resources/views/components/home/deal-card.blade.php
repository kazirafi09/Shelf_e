@props(['book'])

@php
    $originalPrice = $book->display_price;
    $salePrice     = (float) $book->sale_price;
    $discount      = $originalPrice > 0
        ? round((($originalPrice - $salePrice) / $originalPrice) * 100)
        : 0;
    $endsAt        = $book->sale_ends_at->toIso8601String();
@endphp

<div
    class="relative flex gap-4 p-4 transition-shadow duration-300 border border-border bg-card text-card-foreground rounded-xl hover:shadow-lg overflow-hidden group"
    x-data="{
        saleEndsAt: new Date({{ Js::from($endsAt) }}),
        seg: ['00','00','00','00'],
        expired: false,
        tick() {
            const diff = this.saleEndsAt - new Date();
            if (diff <= 0) { this.expired = true; this.seg = ['00','00','00','00']; return; }
            this.seg = [
                String(Math.floor(diff / 86400000)).padStart(2,'0'),
                String(Math.floor((diff % 86400000) / 3600000)).padStart(2,'0'),
                String(Math.floor((diff % 3600000) / 60000)).padStart(2,'0'),
                String(Math.floor((diff % 60000) / 1000)).padStart(2,'0')
            ];
        }
    }"
    x-init="tick(); setInterval(() => tick(), 1000)"
>
    {{-- Discount badge --}}
    @if($discount > 0)
    <div class="absolute top-3 left-3 z-10 px-2 py-0.5 text-[11px] font-black tracking-wide text-white bg-red-500 rounded-full">
        -{{ $discount }}%
    </div>
    @endif

    {{-- Book cover --}}
    <a href="/product/{{ $book->slug }}" class="shrink-0 w-24 sm:w-28">
        <div class="w-full aspect-[2/3] rounded-lg overflow-hidden bg-muted shadow-sm">
            @if($book->image_path)
                <img src="{{ asset('storage/' . $book->image_path) }}"
                     alt="{{ $book->title }}"
                     loading="lazy"
                     class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-105">
            @else
                <div class="flex items-center justify-center w-full h-full text-xs font-medium tracking-widest uppercase text-muted-foreground">
                    Cover
                </div>
            @endif
        </div>
    </a>

    {{-- Details --}}
    <div class="flex flex-col flex-1 min-w-0 py-1">

        {{-- Title & author --}}
        <a href="/product/{{ $book->slug }}" class="group">
            <h3 class="font-bold leading-snug text-foreground truncate transition-colors group-hover:text-gray-500"
                title="{{ $book->title }}">{{ $book->title }}</h3>
            <p class="mt-0.5 text-xs text-muted-foreground truncate">{{ $book->author }}</p>
        </a>

        {{-- Star rating --}}
        @if(($book->approved_reviews_avg_rating ?? 0) > 0)
        <div class="flex items-center gap-1 mt-2">
            <svg class="w-3.5 h-3.5 text-yellow-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            <span class="text-xs font-semibold text-foreground">{{ number_format($book->approved_reviews_avg_rating, 1) }}</span>
        </div>
        @endif

        {{-- Prices --}}
        <div class="flex items-baseline gap-2 mt-2">
            <span class="text-xl font-black text-gray-900">৳ {{ number_format($salePrice, 0) }}</span>
            <span class="text-xs line-through text-gray-400">৳ {{ number_format($originalPrice, 0) }}</span>
        </div>

        {{-- Countdown timer --}}
        <div class="mt-2 mb-3">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-muted-foreground mb-1">Sale ends in</p>
            <div x-show="!expired" class="flex items-center gap-1.5">
                @foreach([['idx' => 0, 'label' => 'D'], ['idx' => 1, 'label' => 'H'], ['idx' => 2, 'label' => 'M'], ['idx' => 3, 'label' => 'S']] as $unit)
                <div class="flex items-center gap-1.5">
                    <div class="flex flex-col items-center">
                        <span x-text="seg[{{ $unit['idx'] }}]"
                              class="inline-flex items-center justify-center w-9 h-8 text-sm font-black tabular-nums bg-gray-900 text-white rounded-md leading-none"></span>
                        <span class="text-[9px] font-bold uppercase tracking-widest text-muted-foreground mt-0.5">{{ $unit['label'] }}</span>
                    </div>
                    @if(!$loop->last)
                    <span class="text-gray-300 font-bold text-sm mb-3">:</span>
                    @endif
                </div>
                @endforeach
            </div>
            <p x-show="expired" class="text-xs font-semibold text-red-500">Sale has ended</p>
        </div>

        {{-- Action buttons --}}
        <div class="flex items-center gap-2 mt-auto">
            <form action="{{ route('cart.add', $book->id) }}" method="POST" class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full py-2 text-xs font-bold text-white transition-colors bg-gray-800 rounded-md hover:bg-gray-700 active:scale-[0.98]">
                    Add to Cart
                </button>
            </form>

            @auth
                @php $isWishlisted = \App\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $book->id)->exists(); @endphp
                <form action="{{ route('wishlist.toggle', $book->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="flex items-center justify-center w-9 h-9 border rounded-md transition hover:scale-110 border-border
                                   {{ $isWishlisted ? 'text-red-500 bg-red-50 border-red-200' : 'text-gray-400 hover:text-red-500 hover:bg-red-50 hover:border-red-200' }}">
                        <svg class="w-4 h-4 {{ $isWishlisted ? 'fill-current' : 'fill-none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                   class="flex items-center justify-center w-9 h-9 border border-border rounded-md text-gray-400 hover:text-red-500 hover:bg-red-50 hover:border-red-200 transition">
                    <svg class="w-4 h-4 fill-none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </a>
            @endauth
        </div>

    </div>
</div>
