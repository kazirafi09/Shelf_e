@extends('layouts.admin')

@section('title', 'Book Scraper')
@section('subtitle', 'Search Book Tank BD and import book data directly into your catalog.')

@section('admin-content')

<div
    x-data="{
        query: '',
        results: [],
        isLoading: false,
        error: null,

        // Reactive category list — quick-add will push new entries here
        // and all per-row selects will update automatically.
        categories: @json($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])),

        // Quick-add modal state
        showCategoryModal: false,
        newCategoryName: '',
        categoryError: null,
        categorySaving: false,

        async search() {
            if (this.query.trim().length < 2) {
                this.error = 'Please enter at least 2 characters to search.';
                return;
            }

            this.isLoading = true;
            this.results  = [];
            this.error    = null;

            try {
                const res = await fetch('{{ route('admin.scraper.search') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ query: this.query }),
                });

                if (!res.ok) {
                    this.error = 'The search service returned an error. Please try again.';
                    return;
                }

                const data = await res.json();

                if (data.error) {
                    this.error = data.error;
                    return;
                }

                this.results = data;
            } catch (e) {
                this.error = 'Network error. Please check your connection.';
            } finally {
                this.isLoading = false;
            }
        },

        async createCategory() {
            if (this.newCategoryName.trim().length < 2) return;
            this.categorySaving = true;
            this.categoryError = null;
            try {
                const res = await fetch('{{ route('admin.categories.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ name: this.newCategoryName }),
                });
                const data = await res.json();
                if (!res.ok) {
                    this.categoryError = data.errors?.name?.[0] ?? 'Something went wrong.';
                } else {
                    this.categories.push({ id: data.id, name: data.name });
                    this.newCategoryName = '';
                    this.showCategoryModal = false;
                }
            } catch (e) {
                this.categoryError = 'Network error.';
            } finally {
                this.categorySaving = false;
            }
        }
    }"
    class="space-y-6"
>

    {{-- Search Bar --}}
    <div class="p-6 bg-card text-card-foreground border border-border shadow-sm rounded-2xl">
        <form @submit.prevent="search()" class="flex gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                    <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    type="text"
                    x-model="query"
                    @input="error = null"
                    @keydown.enter.prevent="search()"
                    placeholder="Search by book title or author, e.g. 'Humayun Ahmed'…"
                    class="block w-full py-3 pl-10 pr-4 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-xl shadow-sm"
                >
            </div>
            <button
                type="submit"
                :disabled="isLoading"
                class="inline-flex items-center gap-2 px-6 py-3 text-sm font-bold transition rounded-xl bg-primary text-primary-foreground hover:bg-primary/90 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <svg x-show="isLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span x-text="isLoading ? 'Searching…' : 'Search'"></span>
            </button>
        </form>
    </div>

    {{-- Error Banner --}}
    <div x-show="error" x-transition.opacity class="p-4 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-xl" style="display: none;">
        <span x-text="error"></span>
    </div>

    {{-- Loading Spinner --}}
    <div x-show="isLoading && results.length === 0" x-transition.opacity class="flex flex-col items-center justify-center py-20" style="display: none;">
        <svg class="w-10 h-10 text-primary animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        <p class="mt-3 text-sm font-medium text-muted-foreground">Searching Book Tank BD…</p>
    </div>

    {{-- Empty State --}}
    <div x-show="!isLoading && results.length === 0 && !error && query.length > 0" x-transition.opacity class="flex flex-col items-center justify-center py-20 bg-card text-card-foreground border border-border rounded-2xl shadow-sm" style="display: none;">
        <svg class="w-12 h-12 mb-3 text-border" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <p class="text-sm font-medium text-muted-foreground">No results found on Book Tank BD. Try a different title.</p>
    </div>

    {{-- Results Table --}}
    <div x-show="results.length > 0" x-transition.opacity class="overflow-hidden bg-card text-card-foreground border border-border shadow-sm rounded-2xl" style="display: none;">

        <div class="px-6 py-4 border-b border-border flex items-center justify-between">
            <p class="text-sm font-semibold text-foreground">
                <span x-text="results.length"></span> results from Book Tank BD
            </p>
            <div class="flex items-center gap-4">
                {{-- Quick-Add Category button --}}
                <button type="button" @click="showCategoryModal = true"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-cyan-700 hover:text-cyan-900 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Category
                </button>
                <a href="https://booktankbd.com" target="_blank" rel="noopener" class="text-xs text-muted-foreground hover:text-foreground transition-colors">
                    booktankbd.com ↗
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-muted">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold tracking-wider text-muted-foreground uppercase">Cover</th>
                        <th class="px-6 py-3 text-xs font-bold tracking-wider text-muted-foreground uppercase">Title</th>
                        <th class="px-6 py-3 text-xs font-bold tracking-wider text-muted-foreground uppercase">Author</th>
                        <th class="px-6 py-3 text-xs font-bold tracking-wider text-muted-foreground uppercase">Price</th>
                        <th class="px-6 py-3 text-xs font-bold tracking-wider text-muted-foreground uppercase">Category</th>
                        <th class="px-6 py-3 text-xs font-bold tracking-wider text-muted-foreground uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <template x-for="(book, index) in results" :key="index">
                        <tr class="transition hover:bg-muted/50">

                            {{-- Cover --}}
                            <td class="px-6 py-4">
                                <template x-if="book.cover_url">
                                    <img :src="book.cover_url" :alt="book.title"
                                         class="object-cover w-10 h-14 rounded-md shadow-sm border border-border"
                                         onerror="this.style.display='none'">
                                </template>
                                <template x-if="!book.cover_url">
                                    <div class="flex items-center justify-center w-10 h-14 rounded-md bg-muted border border-border">
                                        <svg class="w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                </template>
                            </td>

                            {{-- Title --}}
                            <td class="px-6 py-4 max-w-xs">
                                <p class="text-sm font-semibold text-foreground line-clamp-2" x-text="book.title || '—'"></p>
                            </td>

                            {{-- Author --}}
                            <td class="px-6 py-4">
                                <p class="text-sm text-muted-foreground" x-text="book.author || '—'"></p>
                            </td>

                            {{-- Price --}}
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-foreground" x-text="book.price ? '৳ ' + parseFloat(book.price).toFixed(0) : '—'"></p>
                            </td>

                            {{-- Category select (reactive — updates when a new category is created) --}}
                            <td class="px-6 py-4">
                                <form :id="'import-form-' + index" action="{{ route('admin.scraper.import') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="handle"    :value="book.handle">
                                    <input type="hidden" name="title"     :value="book.title">
                                    <input type="hidden" name="author"    :value="book.author">
                                    <input type="hidden" name="price"     :value="book.price">
                                    <input type="hidden" name="cover_url" :value="book.cover_url">
                                    <select name="category_id"
                                            class="w-full px-2 py-1.5 text-xs bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg">
                                        <option value="">— Uncategorized —</option>
                                        <template x-for="cat in categories" :key="cat.id">
                                            <option :value="cat.id" x-text="cat.name"></option>
                                        </template>
                                    </select>
                                </form>
                            </td>

                            {{-- Import button (submits the form for this row) --}}
                            <td class="px-6 py-4">
                                <button
                                    type="submit"
                                    :form="'import-form-' + index"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold transition bg-primary text-primary-foreground hover:bg-primary/90 rounded-lg active:scale-95 shrink-0"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Import
                                </button>
                            </td>

                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Quick-Add Category Modal --}}
    <div
        x-show="showCategoryModal"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
        style="display: none;"
        @keydown.escape.window="showCategoryModal = false"
    >
        <div @click.outside="showCategoryModal = false" class="w-full max-w-sm p-6 bg-card border border-border rounded-2xl shadow-xl">
            <h3 class="mb-1 text-sm font-bold text-foreground">Create New Category</h3>
            <p class="mb-4 text-xs text-muted-foreground">The new category will appear in all import dropdowns instantly.</p>
            <input
                type="text"
                x-model="newCategoryName"
                @keydown.enter.prevent="createCategory()"
                placeholder="Category name…"
                class="block w-full px-4 py-2.5 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-xl shadow-sm mb-2"
            >
            <p x-show="categoryError" x-text="categoryError" class="mb-3 text-xs text-red-600" style="display: none;"></p>
            <div class="flex justify-end gap-3">
                <button type="button" @click="showCategoryModal = false"
                        class="px-4 py-2 text-sm font-medium text-muted-foreground hover:text-foreground transition">Cancel</button>
                <button type="button" @click="createCategory()"
                        :disabled="categorySaving || newCategoryName.trim().length < 2"
                        class="px-5 py-2 text-sm font-bold rounded-xl bg-primary text-primary-foreground hover:bg-primary/90 transition active:scale-95 disabled:opacity-50">
                    <span x-text="categorySaving ? 'Saving…' : 'Create'"></span>
                </button>
            </div>
        </div>
    </div>

</div>

@endsection
