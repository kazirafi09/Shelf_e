@extends('layouts.app')

@section('content')
<div class="container px-4 py-6 mx-auto md:py-8 max-w-7xl" x-data="{ mounted: false }" x-init="setTimeout(() => mounted = true, 100)">
    
    {{-- FIXED: Removed the redundant 'container px-4 max-w-7xl' here to prevent double padding --}}
    <div class="w-full" 
        x-data="{ 
            mounted: false, 
            activeSlide: 1, 
            totalSlides: 3,
            autoPlayInterval: null,
            startAutoPlay() {
                this.stopAutoPlay(); 
                this.autoPlayInterval = setInterval(() => {
                    this.activeSlide = this.activeSlide === this.totalSlides ? 1 : this.activeSlide + 1;
                }, 6000);
            },
            stopAutoPlay() {
                clearInterval(this.autoPlayInterval);
            }
        }" 
        x-init="setTimeout(() => mounted = true, 50); startAutoPlay()"
        @mouseenter="stopAutoPlay()" 
        @mouseleave="startAutoPlay()">
        
        <div class="relative flex flex-col items-center justify-between overflow-hidden transition-all duration-700 ease-in-out transform shadow-xl rounded-3xl md:flex-row group min-h-[400px] md:min-h-[480px] lg:min-h-[560px]"
            :class="{
                'bg-cyan-500': activeSlide === 1,
                'bg-indigo-600': activeSlide === 2,
                'bg-rose-600': activeSlide === 3,
                'opacity-100 translate-y-0': mounted,
                'opacity-0 translate-y-8': !mounted
            }">
            
            <div class="absolute top-0 right-0 z-0 w-full h-full pointer-events-none bg-gradient-to-bl from-white/10 to-transparent"></div>
            <div class="absolute z-0 w-64 h-64 transition-transform duration-700 transform rounded-full bg-white/10 blur-3xl -top-10 -right-10 group-hover:scale-110"></div>

            <div class="relative z-10 w-full p-8 md:p-12 lg:p-16">

                <div x-show="activeSlide === 1" 
                    x-transition:enter="transition ease-out duration-1000"
                    x-transition:enter-start="opacity-0 transform translate-x-12"
                    x-transition:enter-end="opacity-100 transform translate-x-0"
                    x-transition:leave="transition ease-in duration-800 absolute inset-0"
                    x-transition:leave-start="opacity-100 transform translate-x-0"
                    x-transition:leave-end="opacity-0 transform -translate-x-12"
                    class="flex flex-col items-center justify-between md:flex-row">
                    
                    <div class="text-center md:text-left md:w-1/2">
                        <h1 class="mb-4 text-4xl font-extrabold leading-tight tracking-tight text-white md:text-5xl lg:text-6xl drop-shadow-sm">
                            Best Place To Find<br class="hidden md:block"> Your Favorite Books
                        </h1>
                        <p class="max-w-md mx-auto mb-8 text-base font-medium leading-relaxed md:text-lg text-cyan-50 md:mx-0">
                            Explore our latest releases and must-read books: your next favorite story awaits!
                        </p>
                        <div class="flex flex-col justify-center gap-4 sm:flex-row md:justify-start">
                            <a href="/categories" class="inline-flex items-center justify-center px-8 py-3.5 text-base font-bold text-cyan-900 uppercase tracking-wider transition-all duration-300 bg-white rounded-full shadow-lg hover:bg-orange-500 hover:text-white hover:shadow-orange-500/30 hover:-translate-y-1 active:translate-y-0">
                                Shop New Arrivals 
                                <svg class="w-5 h-5 ml-2 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>
                    
                    <div class="flex justify-center w-full mt-12 md:w-1/2 md:mt-0 md:justify-end">
                        <div class="relative flex items-center justify-center w-48 h-64 border-4 border-white/20 rounded-xl bg-white/10 backdrop-blur-sm sm:w-56 sm:h-72 md:w-64 md:h-80 lg:w-72 lg:h-96 shadow-[0_20px_50px_rgba(0,0,0,0.2)] group-hover:border-white/40 transition-colors duration-500 overflow-hidden">
                            <div class="absolute inset-0 z-10 bg-gradient-to-tr from-black/20 to-transparent"></div>
                            <span class="text-sm font-medium tracking-widest uppercase text-white/70">Featured Book</span>
                        </div>
                    </div>
                </div>

                <div x-show="activeSlide === 2" 
                    style="display: none;"
                    x-transition:enter="transition ease-out duration-1000"
                    x-transition:enter-start="opacity-0 transform translate-x-12"
                    x-transition:enter-end="opacity-100 transform translate-x-0"
                    x-transition:leave="transition ease-in duration-800 absolute inset-0"
                    x-transition:leave-start="opacity-100 transform translate-x-0"
                    x-transition:leave-end="opacity-0 transform -translate-x-12"
                    class="flex flex-col items-center justify-between md:flex-row">
                    
                    <div class="text-center md:text-left md:w-1/2">
                        <h1 class="mb-4 text-4xl font-extrabold leading-tight tracking-tight text-white md:text-5xl lg:text-6xl drop-shadow-sm">
                            Bestsellers<br class="hidden md:block"> This Week
                        </h1>
                        <p class="max-w-md mx-auto mb-8 text-base font-medium leading-relaxed text-indigo-100 md:text-lg md:mx-0">
                            Dive into the books everyone is talking about. Handpicked favorites rated by readers.
                        </p>
                        <div class="flex flex-col justify-center gap-4 sm:flex-row md:justify-start">
                            <a href="/categories" class="inline-flex items-center justify-center px-8 py-3.5 text-base font-bold text-indigo-900 uppercase tracking-wider transition-all duration-300 bg-white rounded-full shadow-lg hover:bg-orange-500 hover:text-white hover:shadow-orange-500/30 hover:-translate-y-1 active:translate-y-0">
                                Explore Top Picks
                                <svg class="w-5 h-5 ml-2 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            </a>
                        </div>
                    </div>
                    
                    <div class="flex justify-center w-full mt-12 md:w-1/2 md:mt-0 md:justify-end">
                        <div class="relative flex items-center justify-center w-48 h-64 border-4 border-white/20 rounded-xl bg-white/10 backdrop-blur-sm sm:w-56 sm:h-72 md:w-64 md:h-80 lg:w-72 lg:h-96 shadow-[0_20px_50px_rgba(0,0,0,0.2)] group-hover:border-white/40 transition-colors duration-500 overflow-hidden">
                            <div class="absolute inset-0 z-10 bg-gradient-to-tr from-black/20 to-transparent"></div>
                            <span class="text-sm font-medium tracking-widest uppercase text-white/70">Top Rated</span>
                        </div>
                    </div>
                </div>

                <div x-show="activeSlide === 3" 
                    style="display: none;"
                    x-transition:enter="transition ease-out duration-1000"
                    x-transition:enter-start="opacity-0 transform translate-x-12"
                    x-transition:enter-end="opacity-100 transform translate-x-0"
                    x-transition:leave="transition ease-in duration-800 absolute inset-0"
                    x-transition:leave-start="opacity-100 transform translate-x-0"
                    x-transition:leave-end="opacity-0 transform -translate-x-12"
                    class="flex flex-col items-center justify-between md:flex-row">
                    
                    <div class="text-center md:text-left md:w-1/2">
                        <div class="inline-flex items-center px-4 py-1.5 mb-4 text-xs font-bold text-rose-600 uppercase tracking-wider bg-white rounded-full shadow-inner">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                            Limited Time Offer
                        </div>
                        <h1 class="mb-4 text-4xl font-extrabold leading-tight tracking-tight text-white md:text-5xl lg:text-6xl drop-shadow-sm">
                            Clearance<br class="hidden md:block"> Book Sale
                        </h1>
                        <p class="max-w-md mx-auto mb-8 text-base font-medium leading-relaxed md:text-lg text-rose-100 md:mx-0">
                            Up to 50% off select titles. Grab your next read before they're gone!
                        </p>
                        <div class="flex flex-col justify-center gap-4 sm:flex-row md:justify-start">
                            <a href="/categories" class="inline-flex items-center justify-center px-8 py-3.5 text-base font-bold text-rose-900 uppercase tracking-wider transition-all duration-300 bg-white rounded-full shadow-lg hover:bg-orange-500 hover:text-white hover:shadow-orange-500/30 hover:-translate-y-1 active:translate-y-0">
                                Shop The Sale
                            </a>
                        </div>
                    </div>
                    
                    <div class="flex justify-center w-full mt-12 md:w-1/2 md:mt-0 md:justify-end">
                        <div class="relative flex items-center justify-center w-48 h-64 border-4 border-rose-300/30 rounded-xl bg-white/10 backdrop-blur-sm sm:w-56 sm:h-72 md:w-64 md:h-80 lg:w-72 lg:h-96 shadow-[0_20px_50px_rgba(0,0,0,0.2)] group-hover:border-rose-300/50 transition-colors duration-500 overflow-hidden">
                            <div class="absolute inset-0 z-10 bg-gradient-to-tr from-black/20 to-transparent"></div>
                            <span class="text-sm font-medium tracking-widest uppercase text-rose-100/80">Huge Deals</span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 z-20 flex space-x-2.5 pointer-events-auto">
                <template x-for="index in totalSlides" :key="index">
                    <button @click="stopAutoPlay(); activeSlide = index; startAutoPlay()" 
                            class="w-2.5 h-2.5 rounded-full transition-all duration-300 focus:outline-none"
                            :class="activeSlide === index ? 'bg-white scale-125 shadow-[0_0_10px_rgba(255,255,255,0.8)]' : 'bg-white/40 hover:bg-white'"></button>
                </template>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 mt-8 transition-all duration-700 ease-out delay-300 transform md:grid-cols-4" :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
        <div class="flex items-center p-4 transition-shadow bg-white border border-gray-100 shadow-sm rounded-2xl hover:shadow-md">
            <div class="flex items-center justify-center w-12 h-12 rounded-full shrink-0 bg-cyan-50 text-cyan-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg></div>
            <div class="ml-4">
                <h4 class="text-sm font-bold text-gray-900">Fast Delivery</h4>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider">Nationwide shipping</p>
            </div>
        </div>
        <div class="flex items-center p-4 transition-shadow bg-white border border-gray-100 shadow-sm rounded-2xl hover:shadow-md">
            <div class="flex items-center justify-center w-12 h-12 text-orange-500 rounded-full shrink-0 bg-orange-50"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg></div>
            <div class="ml-4">
                <h4 class="text-sm font-bold text-gray-900">Secure Payment</h4>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider">100% safe checkout</p>
            </div>
        </div>
        <div class="flex items-center p-4 transition-shadow bg-white border border-gray-100 shadow-sm rounded-2xl hover:shadow-md">
            <div class="flex items-center justify-center w-12 h-12 rounded-full shrink-0 bg-cyan-50 text-cyan-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg></div>
            <div class="ml-4">
                <h4 class="text-sm font-bold text-gray-900">Easy Returns</h4>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider">7-day return policy</p>
            </div>
        </div>
        <div class="flex items-center p-4 transition-shadow bg-white border border-gray-100 shadow-sm rounded-2xl hover:shadow-md">
            <div class="flex items-center justify-center w-12 h-12 text-orange-500 rounded-full shrink-0 bg-orange-50"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
            <div class="ml-4">
                <h4 class="text-sm font-bold text-gray-900">Original Books</h4>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider">Guaranteed authentic</p>
            </div>
        </div>
    </div>

    <div class="mt-20 transition-all duration-700 ease-out delay-700 transform" :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-extrabold tracking-tight text-gray-900 md:text-3xl">Shop By Format</h2>
        </div>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <a href="#" class="relative p-8 overflow-hidden transition-colors bg-white border border-gray-200 shadow-sm rounded-3xl group hover:border-cyan-500 hover:shadow-lg">
                <div class="absolute top-0 right-0 p-6 transition-transform duration-500 opacity-5 group-hover:scale-110 group-hover:-rotate-12"><svg class="w-32 h-32 text-cyan-900" fill="currentColor" viewBox="0 0 20 20"><path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"></path></svg></div>
                <h3 class="relative z-10 mb-2 text-xl font-bold text-gray-900">Physical Books</h3>
                <p class="relative z-10 mb-4 text-sm text-gray-500">The classic feel of paper.</p>
                <span class="relative z-10 text-sm font-bold text-cyan-600 group-hover:text-cyan-700">Explore &rarr;</span>
            </a>
            <a href="#" class="relative p-8 overflow-hidden transition-colors bg-white border border-gray-200 shadow-sm rounded-3xl group hover:border-cyan-500 hover:shadow-lg">
                <div class="absolute top-0 right-0 p-6 transition-transform duration-500 opacity-5 group-hover:scale-110 group-hover:-rotate-12"><svg class="w-32 h-32 text-cyan-900" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5zm11 1H6v8l4-2 4 2V6z" clip-rule="evenodd"></path></svg></div>
                <h3 class="relative z-10 mb-2 text-xl font-bold text-gray-900">eBooks</h3>
                <p class="relative z-10 mb-4 text-sm text-gray-500">Read instantly on any device.</p>
                <span class="relative z-10 text-sm font-bold text-cyan-600 group-hover:text-cyan-700">Explore &rarr;</span>
            </a>
            <a href="#" class="relative p-8 overflow-hidden transition-colors bg-white border border-gray-200 shadow-sm rounded-3xl group hover:border-cyan-500 hover:shadow-lg">
                <div class="absolute top-0 right-0 p-6 transition-transform duration-500 opacity-5 group-hover:scale-110 group-hover:-rotate-12"><svg class="w-32 h-32 text-cyan-900" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 10a5.984 5.984 0 01-1.757 4.243 1 1 0 01-1.415-1.415A3.984 3.984 0 0013 10a3.983 3.983 0 00-1.172-2.828 1 1 0 010-1.415z" clip-rule="evenodd"></path></svg></div>
                <h3 class="relative z-10 mb-2 text-xl font-bold text-gray-900">Audiobooks</h3>
                <p class="relative z-10 mb-4 text-sm text-gray-500">Listen to stories on the go.</p>
                <span class="relative z-10 text-sm font-bold text-cyan-600 group-hover:text-cyan-700">Explore &rarr;</span>
            </a>
        </div>
    </div>

    <div class="mt-20 transition-all duration-700 ease-out delay-700 transform" :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
        <div class="flex items-end justify-between mb-8">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-gray-900 md:text-3xl">Popular Authors</h2>
                <p class="mt-1 text-sm text-gray-500">Discover the minds behind the masterpieces.</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($popularAuthors as $item)
            
            {{-- UPDATE: The href now points to the categories route with the author parameter --}}
            <a href="{{ route('categories.index', ['authors[]' => $item->author]) }}" class="flex items-center p-5 transition-all duration-300 bg-white border border-gray-100 shadow-sm cursor-pointer rounded-2xl hover:shadow-md hover:-translate-y-1 group">
                
                <div class="flex items-center justify-center w-16 h-16 overflow-hidden text-2xl font-black text-white transition-all duration-500 rounded-full shrink-0 bg-cyan-500 group-hover:bg-cyan-600 group-hover:scale-110 group-hover:shadow-inner">
                    {{ substr($item->author, 0, 1) }}
                </div>
                
                <div class="ml-5 overflow-hidden">
                    <h3 class="text-lg font-bold text-gray-900 truncate transition-colors duration-300 group-hover:text-cyan-600" title="{{ $item->author }}">{{ $item->author }}</h3>
                    <span class="inline-flex items-center mt-1 text-sm font-bold text-orange-500 transition-colors hover:text-orange-600">
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
                <h2 class="text-2xl font-extrabold tracking-tight text-gray-900 md:text-3xl">Top Rated Books</h2>
                <p class="mt-1 text-sm text-gray-500">Handpicked favorites loved by our community.</p>
            </div>
            <a href="/categories" class="hidden text-sm font-bold tracking-wider uppercase transition-colors text-cyan-600 hover:text-cyan-800 md:inline-block">See All Collection &rarr;</a>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 md:gap-6">
            @foreach($topBooks as $book)
            <div class="relative flex flex-col p-4 overflow-hidden transition-all duration-500 bg-white border border-gray-100 rounded-2xl group hover:shadow-xl hover:-translate-y-2 hover:border-cyan-100">
                
                <button class="absolute z-20 p-2 text-gray-300 transition-colors duration-300 transform -translate-y-2 rounded-full shadow-sm opacity-0 bg-white/80 backdrop-blur-sm top-6 right-6 hover:text-orange-500 hover:bg-white group-hover:opacity-100 group-hover:translate-y-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </button>
                
                <a href="/product/{{ $book->slug }}" class="z-10 block grow">
                    <div class="relative flex items-center justify-center w-full mb-4 overflow-hidden text-gray-400 bg-gray-100 shadow-sm aspect-[2/3] rounded-lg">
                        <div class="absolute inset-0 z-10 transition-opacity duration-300 pointer-events-none bg-black/0 group-hover:bg-black/5"></div>
                        @if($book->image_path)
                            <img src="{{ asset('storage/' . $book->image_path) }}" alt="{{ $book->title }}" class="object-cover w-full h-full transition-transform duration-700 ease-out group-hover:scale-110">
                        @else
                            <span class="text-xs font-medium tracking-widest uppercase">Cover</span>
                        @endif
                    </div>
                    
                    <div class="flex items-center mb-1.5 space-x-1">
                        <svg class="w-4 h-4 text-yellow-400 drop-shadow-sm" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <span class="text-xs font-bold text-gray-700">{{ $book->rating }}</span>
                    </div>
                    
                    <h3 class="font-bold text-gray-900 truncate transition-colors duration-300 group-hover:text-cyan-600" title="{{ $book->title }}">{{ $book->title }}</h3>
                    <p class="mb-3 text-xs font-medium text-gray-500 truncate">{{ $book->author }}</p>
                </a>
                
                <div class="z-10 flex flex-col justify-between pt-3 mt-auto border-t border-gray-100 h-[88px]">
                    <p class="text-lg font-extrabold text-gray-900 transition-transform duration-300 transform group-hover:-translate-y-1">৳ {{ number_format($book->price, 0) }}</p>
                    
                    <form action="{{ route('cart.add', $book->id) }}" method="POST" class="absolute transition-all duration-300 ease-out translate-y-8 opacity-0 left-4 right-4 bottom-4 group-hover:translate-y-0 group-hover:opacity-100">
                        @csrf
                        <button type="submit" class="w-full py-2.5 text-sm font-bold text-white transition-all bg-orange-500 shadow-md rounded-lg hover:bg-orange-600 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0">
                            Add to Cart
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-8 text-center md:hidden">
            <a href="/categories" class="inline-block px-8 py-3 text-sm font-bold transition-colors border-2 rounded-full text-cyan-700 border-cyan-100 hover:bg-cyan-50">See All Books</a>
        </div>
    </div>

</div>
@endsection