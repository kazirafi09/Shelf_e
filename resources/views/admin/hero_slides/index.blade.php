@extends('layouts.admin')

@section('title', 'Carousel Management')
@section('subtitle', 'Search for a book and add it to the homepage slider.')

@section('admin-content')
<div class="max-w-6xl mx-auto space-y-8">

    {{-- Alert Message --}}
    @if(session('success'))
        <div class="p-4 text-sm font-bold text-green-700 bg-green-100 border border-green-200 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Add a slide panel ── --}}
    <div class="p-8 bg-card text-card-foreground border border-border shadow-xl rounded-3xl"
         x-data="{
            query: '',
            results: [],
            selected: null,
            loading: false,
            order: 0,

            async search() {
                if (this.query.length < 1) { this.results = []; return; }
                this.loading = true;
                const res = await fetch('{{ route('admin.hero-slides.search') }}?q=' + encodeURIComponent(this.query));
                this.results = await res.json();
                this.loading = false;
            },

            pick(book) {
                this.selected = book;
                this.query    = book.title;
                this.results  = [];
            },

            clear() {
                this.selected = null;
                this.query    = '';
                this.results  = [];
            }
         }">

        <h2 class="mb-6 text-base font-bold tracking-wide text-foreground uppercase">Add Book to Carousel</h2>

        <form action="{{ route('admin.hero-slides.store') }}" method="POST">
            @csrf

            <input type="hidden" name="product_id" :value="selected ? selected.id : ''">
            <input type="hidden" name="order" :value="order">

            <div class="flex flex-col gap-6 md:flex-row md:items-end">

                {{-- Search box --}}
                <div class="relative flex-1">
                    <label class="block mb-2 text-xs font-bold tracking-widest text-muted-foreground uppercase">Book Title / Author</label>

                    <div class="relative">
                        <input
                            type="text"
                            x-model="query"
                            @input.debounce.300ms="search()"
                            @keydown.escape="results = []"
                            placeholder="Search books…"
                            autocomplete="off"
                            class="w-full px-5 py-3 pr-10 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-xl transition-all"
                        >
                        {{-- Clear button --}}
                        <button type="button" x-show="query" @click="clear()"
                                class="absolute inset-y-0 right-3 flex items-center text-muted-foreground hover:text-foreground">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Dropdown results --}}
                    <div x-show="results.length > 0" x-cloak
                         class="absolute z-20 w-full mt-1 bg-white border border-border rounded-xl shadow-xl overflow-hidden">
                        <template x-for="book in results" :key="book.id">
                            <button type="button"
                                    @click="pick(book)"
                                    class="flex items-center w-full gap-3 px-4 py-3 text-left bg-white hover:bg-gray-50 transition-colors">
                                <img :src="book.image_url ?? ''" x-show="book.image_url"
                                     class="w-8 h-11 object-cover rounded shadow-sm shrink-0">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-foreground truncate" x-text="book.title"></p>
                                    <p class="text-xs text-muted-foreground truncate" x-text="book.author"></p>
                                </div>
                            </button>
                        </template>
                    </div>

                    {{-- Loading indicator --}}
                    <p x-show="loading" x-cloak class="mt-1 text-xs text-muted-foreground">Searching…</p>

                    @error('product_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Order input --}}
                <div class="w-32 shrink-0">
                    <label class="block mb-2 text-xs font-bold tracking-widest text-muted-foreground uppercase">Order</label>
                    <input type="number" x-model.number="order" min="0"
                           class="w-full px-5 py-3 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-xl transition-all">
                </div>

                {{-- Submit --}}
                <button type="submit"
                        :disabled="!selected"
                        class="px-8 py-3 text-sm font-bold text-white transition-all bg-cyan-600 rounded-xl hover:bg-cyan-700 active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed shrink-0">
                    Add Slide
                </button>
            </div>

            {{-- Selected preview --}}
            <div x-show="selected" x-cloak
                 class="flex items-center gap-4 mt-5 p-4 bg-muted/50 border border-border rounded-xl">
                <img :src="selected?.image_url ?? ''" x-show="selected?.image_url"
                     class="w-10 h-14 object-cover rounded shadow">
                <div>
                    <p class="text-sm font-bold text-foreground" x-text="selected?.title"></p>
                    <p class="text-xs text-muted-foreground" x-text="selected?.author"></p>
                </div>
                <span class="ml-auto text-xs font-semibold text-green-600 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full">Selected</span>
            </div>
        </form>
    </div>

    {{-- ── Current slides table ── --}}
    <div class="overflow-hidden bg-card text-card-foreground border border-border shadow-xl rounded-2xl">
        <div class="px-8 py-5 border-b border-border">
            <h2 class="text-base font-bold text-foreground">Current Slides <span class="ml-2 text-sm font-normal text-muted-foreground">({{ $slides->count() }})</span></h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-border bg-muted">
                        <th class="px-8 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase">Cover</th>
                        <th class="px-8 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase">Book</th>
                        <th class="px-8 py-4 text-xs font-bold tracking-wider text-center text-muted-foreground uppercase">Order</th>
                        <th class="px-8 py-4 text-xs font-bold tracking-wider text-right text-muted-foreground uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($slides as $slide)
                    @php
                        $imageUrl = $slide->product?->image_path
                            ? asset('storage/' . $slide->product->image_path)
                            : ($slide->image_path ? asset('storage/' . $slide->image_path) : null);
                        $title  = $slide->product?->title  ?? $slide->title  ?? '—';
                        $author = $slide->product?->author ?? $slide->tag    ?? '—';
                    @endphp
                    <tr class="transition-colors hover:bg-muted/30 group">
                        <td class="px-8 py-4">
                            <div class="w-16 h-20 overflow-hidden border border-border rounded-lg shadow-md transition-transform group-hover:scale-105">
                                @if($imageUrl)
                                    <img src="{{ $imageUrl }}" class="object-cover w-full h-full">
                                @else
                                    <div class="flex items-center justify-center w-full h-full text-xs text-muted-foreground bg-muted">No img</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-4">
                            <p class="text-sm font-bold text-foreground group-hover:text-cyan-600 transition-colors">{{ $title }}</p>
                            <p class="text-xs text-muted-foreground">{{ $author }}</p>
                        </td>
                        <td class="px-8 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-foreground bg-muted rounded-full">
                                {{ $slide->order }}
                            </span>
                        </td>
                        <td class="px-8 py-4 text-right">
                            <form action="{{ route('admin.hero-slides.destroy', $slide) }}" method="POST"
                                  onsubmit="return confirm('Remove this slide from the carousel?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 transition-colors hover:text-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-16 text-center">
                            <svg class="w-12 h-12 mx-auto mb-3 text-border" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="font-medium text-muted-foreground">No slides yet. Search for a book above to add one.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
