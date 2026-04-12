@extends('layouts.admin')

@section('title', 'Hero Books')
@section('subtitle', 'Manage the featured books shown in the hero section on the homepage.')

@section('admin-content')

    {{-- Flash messages --}}
    @if(session('success'))
        <div x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 3500)"
             x-show="show"
             x-transition
             class="flex items-center gap-3 px-5 py-3 mb-6 text-sm font-semibold text-green-800 border border-green-200 rounded-xl bg-green-50">
            <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 mb-6 text-sm text-red-700 bg-red-100 border border-red-200 rounded-xl">
            <ul class="pl-5 list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

        {{-- ─── Add Form ─────────────────────────────────────────────────── --}}
        <div class="lg:col-span-1">
            <div class="p-6 border shadow-sm bg-card text-card-foreground border-border rounded-2xl ring-1 ring-gray-900/5">
                <h3 class="mb-1 text-base font-bold text-foreground">Add Hero Book</h3>
                <p class="mb-5 text-xs text-muted-foreground">Search your catalog and pick a book to feature on the homepage.</p>

                <form
                    action="{{ route('admin.hero-books.store') }}"
                    method="POST"
                    x-data="{
                        query: '',
                        results: [],
                        selectedBook: null,
                        open: false,
                        async search() {
                            if (this.query.length < 2) { this.results = []; this.open = false; return; }
                            const res = await fetch('{{ route('admin.books.search') }}?q=' + encodeURIComponent(this.query));
                            this.results = await res.json();
                            this.open = this.results.length > 0;
                        },
                        select(book) {
                            this.selectedBook = book;
                            this.query = book.title;
                            this.results = [];
                            this.open = false;
                        },
                        clear() {
                            this.selectedBook = null;
                            this.query = '';
                        }
                    }"
                >
                    @csrf

                    {{-- Book Search --}}
                    <div class="mb-4">
                        <label class="block mb-1 text-xs font-bold text-foreground">
                            Book <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input
                                type="text"
                                x-model="query"
                                @input.debounce.300ms="search()"
                                @keydown.escape="open = false"
                                @click.outside="open = false"
                                placeholder="Type title or author…"
                                autocomplete="off"
                                class="block w-full px-4 py-2.5 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-xl shadow-sm"
                            >
                            <button
                                type="button"
                                x-show="selectedBook"
                                @click="clear()"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                                style="display:none;"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>

                            {{-- Dropdown --}}
                            <div
                                x-show="open"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="absolute z-20 w-full mt-1 overflow-hidden border shadow-lg bg-background border-border rounded-xl"
                                style="display:none;"
                            >
                                <template x-for="book in results" :key="book.id">
                                    <button
                                        type="button"
                                        @click="select(book)"
                                        class="flex items-center w-full gap-3 px-3 py-2.5 text-sm text-left hover:bg-cyan-50 hover:text-cyan-700 transition-colors"
                                    >
                                        <template x-if="book.image_path">
                                            <img :src="'/storage/' + book.image_path" class="object-cover w-8 h-10 rounded shrink-0">
                                        </template>
                                        <template x-if="!book.image_path">
                                            <div class="w-8 h-10 rounded bg-gray-100 shrink-0"></div>
                                        </template>
                                        <div class="min-w-0">
                                            <p class="font-semibold truncate text-foreground" x-text="book.title"></p>
                                            <p class="text-xs truncate text-muted-foreground" x-text="book.author"></p>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Selected preview chip --}}
                        <div x-show="selectedBook" class="flex items-center gap-3 p-3 mt-2 border border-cyan-200 rounded-xl bg-cyan-50" style="display:none;">
                            <template x-if="selectedBook && selectedBook.image_path">
                                <img :src="'/storage/' + selectedBook.image_path" class="object-cover w-8 h-10 rounded shrink-0">
                            </template>
                            <div class="min-w-0">
                                <p class="text-xs font-bold truncate text-cyan-800" x-text="selectedBook ? selectedBook.title : ''"></p>
                                <p class="text-[11px] text-cyan-600 truncate" x-text="selectedBook ? selectedBook.author : ''"></p>
                            </div>
                        </div>

                        <input type="hidden" name="product_id" :value="selectedBook ? selectedBook.id : ''">
                    </div>

                    {{-- Tag --}}
                    <div class="mb-4">
                        <label class="block mb-1 text-xs font-bold text-foreground">
                            Tag <span class="font-normal text-muted-foreground">(optional)</span>
                        </label>
                        <input type="text" name="tag" value="{{ old('tag') }}"
                               placeholder="e.g. Staff Pick, New Arrival, Bestseller"
                               class="block w-full px-3 py-2 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-xl shadow-sm">
                    </div>

                    {{-- Title override --}}
                    <div class="mb-4">
                        <label class="block mb-1 text-xs font-bold text-foreground">
                            Title Override <span class="font-normal text-muted-foreground">(optional)</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title') }}"
                               placeholder="Leave blank to use book title"
                               class="block w-full px-3 py-2 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-xl shadow-sm">
                    </div>

                    {{-- Order --}}
                    <div class="mb-5">
                        <label class="block mb-1 text-xs font-bold text-foreground">Display Order</label>
                        <input type="number" name="order" value="{{ old('order', $slides->count()) }}" min="0"
                               class="block w-full px-3 py-2 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-xl shadow-sm">
                        <p class="mt-1 text-xs text-muted-foreground">Lower numbers appear first.</p>
                    </div>

                    <button type="submit"
                            class="w-full px-4 py-2.5 text-sm font-bold text-white bg-cyan-600 rounded-xl hover:bg-cyan-700 active:scale-95 transition">
                        Add to Hero
                    </button>
                </form>
            </div>
        </div>

        {{-- ─── Current Slides ───────────────────────────────────────────── --}}
        <div class="lg:col-span-2">
            <div class="overflow-hidden border shadow-sm bg-card text-card-foreground border-border rounded-2xl ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-border">
                    <h3 class="text-base font-bold text-foreground">Current Hero Books</h3>
                    <p class="mt-0.5 text-xs text-muted-foreground">
                        {{ $slides->count() }} book{{ $slides->count() === 1 ? '' : 's' }} featured. Sorted by Order field.
                    </p>
                </div>

                @if($slides->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <svg class="w-12 h-12 mb-3 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <p class="font-medium text-muted-foreground">No hero books yet.</p>
                        <p class="mt-1 text-sm text-muted-foreground">Add a book using the form on the left.</p>
                    </div>
                @else
                    <div class="divide-y divide-border">
                        @foreach($slides as $slide)
                            <div x-data="{ editing: false }" class="p-4">
                                <div class="flex items-center gap-4">

                                    {{-- Order badge --}}
                                    <div class="flex items-center justify-center w-8 h-8 text-xs font-black text-white bg-gray-400 rounded-full shrink-0">
                                        {{ $slide->order }}
                                    </div>

                                    {{-- Cover --}}
                                    <div class="w-10 h-14 overflow-hidden border rounded-lg shrink-0 border-border bg-muted">
                                        @if($slide->product?->image_path)
                                            <img src="{{ asset('storage/' . $slide->product->image_path) }}"
                                                 alt="" class="object-cover w-full h-full">
                                        @else
                                            <div class="flex items-center justify-center w-full h-full text-[9px] text-muted-foreground font-bold uppercase text-center">No Cover</div>
                                        @endif
                                    </div>

                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        @if($slide->tag)
                                            <span class="inline-flex items-center px-2 py-0.5 mb-1 text-[10px] font-bold uppercase tracking-wider rounded-full bg-amber-100 text-amber-700 border border-amber-200">
                                                {{ $slide->tag }}
                                            </span>
                                        @endif
                                        <p class="text-sm font-bold truncate text-foreground">
                                            {{ $slide->title ?: $slide->product?->title ?? '—' }}
                                        </p>
                                        <p class="text-xs truncate text-muted-foreground">{{ $slide->product?->author }}</p>
                                    </div>

                                    {{-- Actions --}}
                                    <div class="flex items-center gap-2 shrink-0">
                                        <button type="button" @click="editing = !editing" title="Edit"
                                                class="inline-flex items-center justify-center w-8 h-8 transition-colors border rounded-lg border-border text-muted-foreground hover:bg-muted">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3z"/>
                                            </svg>
                                        </button>
                                        <form action="{{ route('admin.hero-books.destroy', $slide) }}" method="POST"
                                              x-on:submit.prevent="if(confirm('Remove this book from hero?')) $el.submit()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Remove"
                                                    class="inline-flex items-center justify-center w-8 h-8 text-red-600 transition-colors border border-red-200 rounded-lg hover:bg-red-50">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Inline edit --}}
                                <div x-show="editing"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 -translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="mt-3" style="display:none;">
                                    <form action="{{ route('admin.hero-books.update', $slide) }}" method="POST"
                                          class="flex flex-wrap items-end gap-3 p-4 border rounded-xl border-border bg-muted/30">
                                        @csrf
                                        @method('PUT')
                                        <div class="w-20">
                                            <label class="block mb-1 text-xs font-semibold text-muted-foreground">Order</label>
                                            <input type="number" name="order" value="{{ $slide->order }}" min="0"
                                                   class="block w-full px-2 py-1.5 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg">
                                        </div>
                                        <div class="flex-1 min-w-28">
                                            <label class="block mb-1 text-xs font-semibold text-muted-foreground">Tag</label>
                                            <input type="text" name="tag" value="{{ $slide->tag }}" placeholder="e.g. Staff Pick"
                                                   class="block w-full px-2 py-1.5 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg">
                                        </div>
                                        <div class="flex-1 min-w-36">
                                            <label class="block mb-1 text-xs font-semibold text-muted-foreground">Title Override</label>
                                            <input type="text" name="title" value="{{ $slide->title }}" placeholder="Leave blank for book title"
                                                   class="block w-full px-2 py-1.5 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg">
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="submit"
                                                    class="px-4 py-1.5 text-xs font-bold text-white bg-cyan-600 rounded-lg hover:bg-cyan-700 active:scale-95 transition">
                                                Save
                                            </button>
                                            <button type="button" @click="editing = false"
                                                    class="px-4 py-1.5 text-xs font-medium transition rounded-lg text-muted-foreground hover:text-foreground">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

@endsection
