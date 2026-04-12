@php
    /**
     * Resolve the best image source for each hero slot.
     * Priority: admin-uploaded WebP (storage) → original static PNG.
     *
     * Uses asset() instead of Storage::url() so the generated URLs
     * honour the current request host/port (fixes APP_URL mismatch
     * when running php artisan serve on a non-default port).
     */
    if (! function_exists('heroCacheBust')) {
        function heroCacheBust(string $storageKey): string
        {
            $disk = \Illuminate\Support\Facades\Storage::disk('public');
            return $disk->exists($storageKey) ? '?v=' . $disk->lastModified($storageKey) : '';
        }
    }

    if (! function_exists('heroSrc')) {
        function heroSrc(int $slot, int $width): string
        {
            $key = "hero/{$slot}_{$width}.webp";
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($key)) {
                return asset("storage/{$key}") . heroCacheBust($key);
            }
            return asset("images/hero/{$slot}.png");
        }
    }

    if (! function_exists('heroFallbackSrc')) {
        function heroFallbackSrc(int $slot): string
        {
            $key = "hero/{$slot}_fallback.png";
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($key)) {
                return asset("storage/{$key}") . heroCacheBust($key);
            }
            return asset("images/hero/{$slot}.png");
        }
    }

    if (! function_exists('heroHasWebp')) {
        function heroHasWebp(int $slot): bool
        {
            return \Illuminate\Support\Facades\Storage::disk('public')->exists("hero/{$slot}_480.webp");
        }
    }
@endphp

<section
    class="relative overflow-hidden"
    x-data="{ mounted: false }"
    x-init="$nextTick(() => { setTimeout(() => mounted = true, 80) })"
>
    {{-- Background gradient wash --}}
    <div class="absolute inset-0 -z-10">
        <div class="absolute top-0 -left-40 w-[600px] h-[600px] rounded-full bg-amber-100/60 blur-3xl"></div>
        <div class="absolute bottom-0 -right-40 w-[500px] h-[500px] rounded-full bg-orange-50/50 blur-3xl"></div>
    </div>

    <div class="relative z-10 px-4 py-16 mx-auto max-w-7xl sm:px-6 lg:px-8 md:py-20 lg:py-28">
        <div class="grid items-center grid-cols-1 gap-10 lg:grid-cols-2 lg:gap-20">

            {{-- ─── LEFT: Content ─────────────────────────────────────────── --}}
            <div
                class="flex flex-col justify-center transition-all duration-700 ease-out"
                :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            >

                {{-- Eyebrow pill --}}
                <div class="inline-flex items-center gap-2 mb-7 self-start px-4 py-1.5 rounded-full bg-amber-50 border border-amber-200/80 shadow-sm">
                    <span class="relative flex w-2 h-2">
                        <span class="absolute inline-flex w-full h-full rounded-full opacity-75 animate-ping bg-amber-400"></span>
                        <span class="relative inline-flex w-2 h-2 rounded-full bg-amber-500"></span>
                    </span>
                    <span class="text-[11px] font-bold tracking-[0.18em] uppercase text-amber-700">
                        Curated for Book Lovers
                    </span>
                </div>

                {{-- Headline --}}
                <h1 class="text-[2.6rem] sm:text-5xl lg:text-[3.6rem] font-black tracking-tight leading-[1.06] mb-6">
                    <span class="text-gray-900">Discover Your</span><br>
                    <span class="relative whitespace-nowrap">
                        <span class="relative z-10 bg-gradient-to-r from-amber-600 via-orange-500 to-rose-500 bg-clip-text text-transparent">Next Great</span>
                        {{-- Animated underline accent --}}
                        <svg class="absolute left-0 w-full -bottom-1.5" viewBox="0 0 320 12" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M2 9 C60 3, 150 3, 318 7" stroke="url(#hero-underline-grad)" stroke-width="6" stroke-linecap="round"/>
                            <defs>
                                <linearGradient id="hero-underline-grad" x1="0" y1="0" x2="320" y2="0" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#F59E0B"/>
                                    <stop offset="0.5" stop-color="#F97316"/>
                                    <stop offset="1" stop-color="#E11D48"/>
                                </linearGradient>
                            </defs>
                        </svg>
                    </span><br>
                    <span class="text-gray-900">Read.</span>
                </h1>

                {{-- Subheadline --}}
                <p class="max-w-sm mb-9 text-base leading-relaxed text-gray-500 sm:text-lg lg:max-w-md">
                    From timeless classics to fresh new voices — find the story that speaks to you. Handpicked titles, delivered to your door.
                </p>

                {{-- CTAs --}}
                <div class="flex flex-wrap items-center gap-4 mb-11">
                    <a
                        href="{{ route('categories.index') }}"
                        class="group/btn relative inline-flex items-center gap-2.5 px-8 py-4 bg-gradient-to-r from-gray-900 to-gray-800 text-white text-sm font-bold tracking-wide rounded-full shadow-lg shadow-gray-900/20 hover:shadow-xl hover:shadow-gray-900/30 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300"
                    >
                        Shop the Collection
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover/btn:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    <a
                        href="{{ route('categories.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-4 text-sm font-bold tracking-wide text-gray-700 transition-all duration-300 border border-gray-200 rounded-full hover:border-gray-300 hover:bg-gray-50 hover:-translate-y-0.5 active:translate-y-0"
                    >
                        Browse Categories
                    </a>
                </div>

                {{-- Stats row — glassmorphism card --}}
                <div class="inline-flex flex-wrap items-center gap-x-7 gap-y-3 px-6 py-4 rounded-2xl bg-white/60 backdrop-blur-sm border border-gray-100 shadow-sm">
                    <div>
                        <p class="text-xl font-black text-gray-900 tabular-nums">1,000+</p>
                        <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 mt-0.5">Titles</p>
                    </div>
                    <div class="hidden w-px bg-gray-200 h-9 sm:block"></div>
                    <div>
                        <p class="text-xl font-black text-gray-900">5.0</p>
                        <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 mt-0.5">Reader Rated</p>
                    </div>
                    <div class="hidden w-px bg-gray-200 h-9 sm:block"></div>
                    <div>
                        <p class="text-xl font-black text-gray-900">Free</p>
                        <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 mt-0.5">Nationwide Delivery</p>
                    </div>
                </div>

            </div>

            {{-- ─── RIGHT: Image Mosaic ─────────────────────────────────── --}}
            <div class="relative">

                {{-- ── Mobile / Tablet mosaic (< lg) ──────────────────── --}}
                <div class="grid gap-2.5 lg:hidden" style="grid-template-columns: 2fr 1fr; grid-template-rows: 1fr 1fr; height: 360px;">

                    <div
                        class="relative overflow-hidden shadow-lg rounded-2xl group ring-1 ring-black/5 transition-all duration-700 ease-out"
                        style="grid-column: 1; grid-row: 1 / 3;"
                        :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                    >
                        @if(heroHasWebp(1))
                            <picture>
                                <source type="image/webp"
                                    srcset="{{ heroSrc(1, 480) }} 480w, {{ heroSrc(1, 960) }} 960w"
                                    sizes="(max-width: 1024px) 66vw, 0">
                                <img src="{{ heroFallbackSrc(1) }}" alt="A desk with a stack of classic books and an open journal"
                                    class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-110"
                                    fetchpriority="high" loading="eager" width="960" height="556">
                            </picture>
                        @else
                            <img src="{{ asset('images/hero/1.png') }}" alt="A desk with a stack of classic books and an open journal"
                                class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-110"
                                fetchpriority="high" loading="eager" width="960" height="556">
                        @endif
                        <div class="absolute inset-0 transition-opacity duration-500 opacity-0 bg-gradient-to-t from-black/30 via-transparent to-transparent group-hover:opacity-100"></div>
                    </div>

                    <div
                        class="relative overflow-hidden shadow-lg rounded-2xl group ring-1 ring-black/5 transition-all duration-700 ease-out delay-100"
                        style="grid-column: 2; grid-row: 1;"
                        :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                    >
                        @if(heroHasWebp(2))
                            <picture>
                                <source type="image/webp"
                                    srcset="{{ heroSrc(2, 480) }} 480w"
                                    sizes="(max-width: 1024px) 33vw, 0">
                                <img src="{{ heroFallbackSrc(2) }}" alt="A vibrant marble journal with a coffee cup and brass bookmark"
                                    class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-110"
                                    loading="eager" width="480" height="278">
                            </picture>
                        @else
                            <img src="{{ asset('images/hero/2.png') }}" alt="A vibrant marble journal with a coffee cup and brass bookmark"
                                class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-110"
                                loading="eager" width="480" height="278">
                        @endif
                        <div class="absolute inset-0 transition-opacity duration-500 opacity-0 bg-gradient-to-t from-amber-600/20 via-transparent to-transparent group-hover:opacity-100"></div>
                    </div>

                    <div
                        class="relative overflow-hidden shadow-lg rounded-2xl group ring-1 ring-black/5 transition-all duration-700 ease-out delay-200"
                        style="grid-column: 2; grid-row: 2;"
                        :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
                    >
                        @if(heroHasWebp(3))
                            <picture>
                                <source type="image/webp"
                                    srcset="{{ heroSrc(3, 480) }} 480w"
                                    sizes="(max-width: 1024px) 33vw, 0">
                                <img src="{{ heroFallbackSrc(3) }}" alt="A cozy blue armchair with a book and a mug"
                                    class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-110"
                                    loading="eager" width="480" height="278">
                            </picture>
                        @else
                            <img src="{{ asset('images/hero/3.png') }}" alt="A cozy blue armchair with a book and a mug"
                                class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-110"
                                loading="eager" width="480" height="278">
                        @endif
                        <div class="absolute inset-0 transition-opacity duration-500 opacity-0 bg-gradient-to-t from-rose-600/20 via-transparent to-transparent group-hover:opacity-100"></div>
                    </div>

                </div>

                {{-- ── Desktop mosaic (lg+) ───────────────────────────── --}}
                <div
                    class="hidden gap-3.5 lg:grid"
                    style="grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(3, 1fr); height: 620px;"
                >

                    {{-- img1: large featured (col 1-2, row 1-2) — PRIMARY LCP ELEMENT --}}
                    <div
                        class="relative overflow-hidden shadow-xl rounded-3xl group ring-1 ring-black/5 transition-all duration-700 ease-out delay-200"
                        style="grid-column: 1 / 3; grid-row: 1 / 3;"
                        :class="mounted ? 'opacity-100 translate-y-0 rotate-0' : 'opacity-0 translate-y-12 rotate-1'"
                    >
                        @if(heroHasWebp(1))
                            <picture>
                                <source type="image/webp"
                                    srcset="{{ heroSrc(1, 480) }} 480w, {{ heroSrc(1, 960) }} 960w, {{ heroSrc(1, 1440) }} 1440w"
                                    sizes="(min-width: 1024px) 44vw, 0">
                                <img src="{{ heroFallbackSrc(1) }}" alt="A desk with a stack of classic books and an open journal"
                                    class="object-cover w-full h-full transition-transform duration-700 ease-out group-hover:scale-105"
                                    fetchpriority="high" loading="eager" width="1440" height="836">
                            </picture>
                        @else
                            <img src="{{ asset('images/hero/1.png') }}" alt="A desk with a stack of classic books and an open journal"
                                class="object-cover w-full h-full transition-transform duration-700 ease-out group-hover:scale-105"
                                fetchpriority="high" loading="eager" width="1440" height="836">
                        @endif
                        <div class="absolute inset-0 transition-opacity duration-500 opacity-0 bg-gradient-to-t from-black/25 via-transparent to-transparent group-hover:opacity-100 rounded-3xl"></div>
                        <div class="absolute inset-0 shadow-[inset_0_0_60px_rgba(0,0,0,0.08)] rounded-3xl pointer-events-none"></div>
                    </div>

                    {{-- img2: top-right (col 3, row 1) --}}
                    <div
                        class="relative overflow-hidden shadow-lg rounded-3xl group ring-1 ring-black/5 transition-all duration-700 ease-out delay-300"
                        style="grid-column: 3; grid-row: 1;"
                        :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    >
                        @if(heroHasWebp(2))
                            <picture>
                                <source type="image/webp"
                                    srcset="{{ heroSrc(2, 480) }} 480w, {{ heroSrc(2, 960) }} 960w"
                                    sizes="(min-width: 1024px) 22vw, 0">
                                <img src="{{ heroFallbackSrc(2) }}" alt="A vibrant marble journal with a coffee cup and brass bookmark"
                                    class="object-cover w-full h-full transition-transform duration-700 ease-out group-hover:scale-110"
                                    loading="eager" width="960" height="556">
                            </picture>
                        @else
                            <img src="{{ asset('images/hero/2.png') }}" alt="A vibrant marble journal with a coffee cup and brass bookmark"
                                class="object-cover w-full h-full transition-transform duration-700 ease-out group-hover:scale-110"
                                loading="eager" width="960" height="556">
                        @endif
                        <div class="absolute inset-0 transition-opacity duration-500 opacity-0 bg-gradient-to-br from-amber-500/20 via-transparent to-transparent group-hover:opacity-100 rounded-3xl"></div>
                    </div>

                    {{-- img3: middle-right (col 3, row 2) --}}
                    <div
                        class="relative overflow-hidden shadow-lg rounded-3xl group ring-1 ring-black/5 transition-all duration-700 ease-out delay-[400ms]"
                        style="grid-column: 3; grid-row: 2;"
                        :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    >
                        @if(heroHasWebp(3))
                            <picture>
                                <source type="image/webp"
                                    srcset="{{ heroSrc(3, 480) }} 480w, {{ heroSrc(3, 960) }} 960w"
                                    sizes="(min-width: 1024px) 22vw, 0">
                                <img src="{{ heroFallbackSrc(3) }}" alt="A cozy blue armchair draped with a knit blanket, book and mug"
                                    class="object-cover w-full h-full transition-transform duration-700 ease-out group-hover:scale-110"
                                    loading="eager" width="960" height="556">
                            </picture>
                        @else
                            <img src="{{ asset('images/hero/3.png') }}" alt="A cozy blue armchair draped with a knit blanket, book and mug"
                                class="object-cover w-full h-full transition-transform duration-700 ease-out group-hover:scale-110"
                                loading="eager" width="960" height="556">
                        @endif
                        <div class="absolute inset-0 transition-opacity duration-500 opacity-0 bg-gradient-to-br from-orange-500/20 via-transparent to-transparent group-hover:opacity-100 rounded-3xl"></div>
                    </div>

                    {{-- img4: bottom-left (col 1, row 3) --}}
                    <div
                        class="relative overflow-hidden shadow-lg rounded-3xl group ring-1 ring-black/5 transition-all duration-700 ease-out delay-500"
                        style="grid-column: 1; grid-row: 3;"
                        :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    >
                        @if(heroHasWebp(4))
                            <picture>
                                <source type="image/webp"
                                    srcset="{{ heroSrc(4, 480) }} 480w"
                                    sizes="(min-width: 1024px) 15vw, 0">
                                <img src="{{ heroFallbackSrc(4) }}" alt="Colorful vintage books on a wooden bookshelf"
                                    class="object-cover w-full h-full transition-transform duration-700 ease-out group-hover:scale-110"
                                    loading="lazy" width="480" height="278">
                            </picture>
                        @else
                            <img src="{{ asset('images/hero/4.png') }}" alt="Colorful vintage books on a wooden bookshelf"
                                class="object-cover w-full h-full transition-transform duration-700 ease-out group-hover:scale-110"
                                loading="lazy" width="480" height="278">
                        @endif
                        <div class="absolute inset-0 transition-opacity duration-500 opacity-0 bg-gradient-to-br from-rose-500/20 via-transparent to-transparent group-hover:opacity-100 rounded-3xl"></div>
                    </div>

                    {{-- img5: bottom-right (col 2-3, row 3) --}}
                    <div
                        class="relative overflow-hidden shadow-lg rounded-3xl group ring-1 ring-black/5 transition-all duration-700 ease-out delay-[600ms]"
                        style="grid-column: 2 / 4; grid-row: 3;"
                        :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                    >
                        @if(heroHasWebp(5))
                            <picture>
                                <source type="image/webp"
                                    srcset="{{ heroSrc(5, 480) }} 480w, {{ heroSrc(5, 960) }} 960w"
                                    sizes="(min-width: 1024px) 30vw, 0">
                                <img src="{{ heroFallbackSrc(5) }}" alt="A person reading a teal book titled A Gathering of Stories"
                                    class="object-cover object-center w-full h-full transition-transform duration-700 ease-out group-hover:scale-110"
                                    loading="lazy" width="960" height="278">
                            </picture>
                        @else
                            <img src="{{ asset('images/hero/5.png') }}" alt="A person reading a teal book titled A Gathering of Stories"
                                class="object-cover object-center w-full h-full transition-transform duration-700 ease-out group-hover:scale-110"
                                loading="lazy" width="960" height="278">
                        @endif
                        <div class="absolute inset-0 transition-opacity duration-500 opacity-0 bg-gradient-to-br from-amber-500/15 via-transparent to-rose-500/15 group-hover:opacity-100 rounded-3xl"></div>
                    </div>

                </div>
            </div>

        </div>
    </div>


</section>
