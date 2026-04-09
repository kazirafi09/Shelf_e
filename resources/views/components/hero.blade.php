<section
    class="relative"
    x-data="{ mounted: false }"
    x-init="$nextTick(() => { setTimeout(() => mounted = true, 80) })"
>


    <div class="relative z-10 px-4 py-16 mx-auto max-w-7xl sm:px-6 lg:px-8 md:py-20 lg:py-24">
        <div class="grid items-center grid-cols-1 gap-10 lg:grid-cols-2 lg:gap-16">

            {{-- ─── LEFT: Content ─────────────────────────────────────────── --}}
            <div
                class="flex flex-col justify-center transition-all duration-700 ease-out"
                :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
            >

                {{-- Eyebrow label --}}
                <div class="inline-flex items-center gap-2.5 mb-6 self-start">
                    <span class="w-10 h-px bg-amber-400"></span>
                    <span class="text-[11px] font-black tracking-[0.22em] uppercase text-amber-600">
                        Curated for Book Lovers
                    </span>
                </div>

                {{-- Headline --}}
                <h1 class="text-[2.6rem] sm:text-5xl lg:text-[3.4rem] font-black tracking-tight text-gray-900 leading-[1.08] mb-5">
                    aDiscover Your<br>
                    <span class="relative whitespace-nowrap">
                        <span class="relative z-10">Next Great</span>
                        {{-- Hand-drawn underline accent --}}
                        <svg class="absolute left-0 w-full -bottom-2" viewBox="0 0 320 12" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M2 9 C60 3, 150 3, 318 7" stroke="#FCD34D" stroke-width="7" stroke-linecap="round"/>
                        </svg>
                    </span><br>
                    Read.
                </h1>

                {{-- Subheadline --}}
                <p class="max-w-sm mb-8 text-base leading-relaxed text-gray-500 sm:text-lg lg:max-w-md">
                    From timeless classics to fresh new voices — find the story that speaks to you. Handpicked titles, delivered to your door.
                </p>

                {{-- CTAs --}}
                <div class="flex flex-wrap items-center gap-3 mb-10">
                    <a
                        href="{{ route('categories.index') }}"
                        class="inline-flex items-center gap-2 px-7 py-3.5 bg-gray-900 text-white text-sm font-bold tracking-wide rounded-full shadow-md hover:bg-gray-700 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200"
                    >
                        Shop the Collection
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>

                {{-- Stats row --}}
                <div class="flex flex-wrap items-center gap-x-6 gap-y-3">
                    <div>
                        <p class="text-xl font-black text-gray-900 tabular-nums">1,000+</p>
                        <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 mt-0.5">Titles</p>
                    </div>
                    <div class="hidden w-px bg-gray-200 h-9 sm:block"></div>
                    <div>
                        <p class="text-xl font-black text-gray-900">⭐ 5.0</p>
                        <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 mt-0.5">Reader Rated</p>
                    </div>
                    <div class="hidden w-px bg-gray-200 h-9 sm:block"></div>
                    <div>
                        
                        <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 mt-0.5">Nationwide Delivery</p>
                    </div>
                </div>

            </div>

            {{-- ─── RIGHT: Image Mosaic ─────────────────────────────────── --}}
            <div
                class="transition-all duration-700 ease-out delay-200"
                :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'"
            >

                {{-- ── Mobile / Tablet mosaic (< lg) ──────────────────── --}}
                {{--
                    Layout (3 cols × 2 rows):
                    ┌──────────────┬──────┐
                    │              │  2   │
                    │      1       ├──────┤
                    │              │  3   │
                    └──────┴───────┴──────┘
                    (imgs 4 & 5 hidden on small screens to keep it clean)
                --}}
                <div class="grid gap-2 lg:hidden" style="grid-template-columns: 2fr 1fr; grid-template-rows: 1fr 1fr; height: 340px;">

                    <div class="overflow-hidden shadow-md rounded-2xl group" style="grid-column: 1; grid-row: 1 / 3;">
                        <img
                            src="{{ asset('images/hero/1.png') }}"
                            alt="A desk with a stack of classic books and an open journal"
                            class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-105"
                            loading="eager"
                        >
                    </div>

                    <div class="overflow-hidden shadow-md rounded-2xl group" style="grid-column: 2; grid-row: 1;">
                        <img
                            src="{{ asset('images/hero/2.png') }}"
                            alt="A vibrant marble journal with a coffee cup and brass bookmark"
                            class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-105"
                            loading="eager"
                        >
                    </div>

                    <div class="overflow-hidden shadow-md rounded-2xl group" style="grid-column: 2; grid-row: 2;">
                        <img
                            src="{{ asset('images/hero/3.png') }}"
                            alt="A cozy blue armchair with a book and a mug"
                            class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-105"
                            loading="eager"
                        >
                    </div>

                </div>

                {{-- ── Desktop mosaic (lg+) ───────────────────────────── --}}
                {{--
                    Layout (3 cols × 3 rows):
                    ┌──────────────┬──────┐
                    │              │  2   │  row 1
                    │      1       ├──────┤
                    │   (2×2)      │  3   │  row 2
                    ├──────┬───────┴──────┤
                    │  4   │      5       │  row 3
                    └──────┴──────────────┘
                --}}
                <div
                    class="hidden gap-3 lg:grid"
                    style="grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(3, 1fr); height: 590px;"
                >

                    {{-- img1: large featured (col 1-2, row 1-2) --}}
                    <div
                        class="relative overflow-hidden shadow-lg rounded-2xl group"
                        style="grid-column: 1 / 3; grid-row: 1 / 3;"
                    >
                        <img
                            src="{{ asset('images/hero/1.png') }}"
                            alt="A desk with a stack of classic books and an open journal"
                            class="object-cover w-full h-full transition-transform duration-700 ease-out group-hover:scale-105"
                            loading="eager"
                        >
                        {{-- Subtle inner shadow for depth --}}
                        <div class="absolute inset-0 shadow-[inset_0_0_40px_rgba(0,0,0,0.06)] rounded-2xl pointer-events-none"></div>
                    </div>

                    {{-- img2: top-right (col 3, row 1) --}}
                    <div
                        class="overflow-hidden shadow-md rounded-2xl group"
                        style="grid-column: 3; grid-row: 1;"
                    >
                        <img
                            src="{{ asset('images/hero/2.png') }}"
                            alt="A vibrant marble journal with a coffee cup and brass bookmark"
                            class="object-cover w-full h-full transition-transform duration-700 ease-out group-hover:scale-105"
                            loading="eager"
                        >
                    </div>

                    {{-- img3: middle-right (col 3, row 2) --}}
                    <div
                        class="overflow-hidden shadow-md rounded-2xl group"
                        style="grid-column: 3; grid-row: 2;"
                    >
                        <img
                            src="{{ asset('images/hero/3.png') }}"
                            alt="A cozy blue armchair draped with a knit blanket, book and mug"
                            class="object-cover w-full h-full transition-transform duration-700 ease-out group-hover:scale-105"
                            loading="eager"
                        >
                    </div>

                    {{-- img4: bottom-left (col 1, row 3) --}}
                    <div
                        class="overflow-hidden shadow-md rounded-2xl group"
                        style="grid-column: 1; grid-row: 3;"
                    >
                        <img
                            src="{{ asset('images/hero/4.png') }}"
                            alt="Colorful vintage books on a wooden bookshelf"
                            class="object-cover w-full h-full transition-transform duration-700 ease-out group-hover:scale-105"
                            loading="eager"
                        >
                    </div>

                    {{-- img5: bottom-right (col 2-3, row 3) --}}
                    <div
                        class="overflow-hidden shadow-md rounded-2xl group"
                        style="grid-column: 2 / 4; grid-row: 3;"
                    >
                        <img
                            src="{{ asset('images/hero/5.png') }}"
                            alt="A person reading a teal book titled A Gathering of Stories"
                            class="object-cover object-center w-full h-full transition-transform duration-700 ease-out group-hover:scale-105"
                            loading="eager"
                        >
                    </div>

                </div>
            </div>

        </div>
    </div>


</section>
