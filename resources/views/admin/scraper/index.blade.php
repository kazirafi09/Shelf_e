@extends('layouts.admin')

@section('title', 'Book Scraper')
@section('subtitle', 'Search Open Library and import book data directly into your catalog.')

@section('admin-content')

<div
    x-data="{
        query: '',
        results: [],
        isLoading: false,
        error: null,

        async search() {
            if (this.query.trim().length < 2) return;

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
        }
    }"
    class="space-y-6"
>

    {{-- Search Bar --}}
    <div class="p-6 bg-card text-card-foreground border border-border shadow-sm rounded-2xl ring-1 ring-gray-900/5">
        <form @submit.prevent="search()" class="flex gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    type="text"
                    x-model="query"
                    @keydown.enter.prevent="search()"
                    placeholder="Search by book title, e.g. 'The Great Gatsby'…"
                    class="block w-full py-3 pl-10 pr-4 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-xl shadow-sm"
                >
            </div>
            <button
                type="submit"
                :disabled="isLoading || query.trim().length < 2"
                class="inline-flex items-center gap-2 px-6 py-3 text-sm font-bold text-white transition rounded-xl bg-cyan-600 hover:bg-cyan-700 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                {{-- Spinner inside button --}}
                <svg x-show="isLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span x-text="isLoading ? 'Searching…' : 'Search'"></span>
            </button>
        </form>
    </div>

    {{-- Error Banner --}}
    <div x-show="error" x-transition.opacity class="p-4 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-xl" style="display: none;">
        <span x-text="error"></span>
    </div>

    {{-- Loading Spinner (full results area) --}}
    <div x-show="isLoading && results.length === 0" x-transition.opacity class="flex flex-col items-center justify-center py-20" style="display: none;">
        <svg class="w-10 h-10 text-cyan-500 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        <p class="mt-3 text-sm font-medium text-muted-foreground">Searching Open Library…</p>
    </div>

    {{-- Empty State --}}
    <div x-show="!isLoading && results.length === 0 && !error && query.length > 0" x-transition.opacity class="flex flex-col items-center justify-center py-20 bg-card text-card-foreground border border-border rounded-2xl shadow-sm ring-1 ring-gray-900/5" style="display: none;">
        <svg class="w-12 h-12 mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <p class="text-sm font-medium text-muted-foreground">No results found. Try a different title.</p>
    </div>

    {{-- Results Table --}}
    <div x-show="results.length > 0" x-transition.opacity class="overflow-hidden bg-card text-card-foreground border border-border shadow-sm rounded-2xl ring-1 ring-gray-900/5" style="display: none;">

        <div class="px-6 py-4 border-b border-border">
            <p class="text-sm font-semibold text-foreground">
                <span x-text="results.length"></span> results from Open Library
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-muted">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold tracking-wider text-muted-foreground uppercase">Cover</th>
                        <th class="px-6 py-3 text-xs font-bold tracking-wider text-muted-foreground uppercase">Title</th>
                        <th class="px-6 py-3 text-xs font-bold tracking-wider text-muted-foreground uppercase">Author</th>
                        <th class="px-6 py-3 text-xs font-bold tracking-wider text-muted-foreground uppercase">Year</th>
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
                                         class="object-cover w-10 h-14 rounded-md shadow-sm ring-1 ring-gray-900/5"
                                         onerror="this.style.display='none'">
                                </template>
                                <template x-if="!book.cover_url">
                                    <div class="flex items-center justify-center w-10 h-14 rounded-md bg-muted ring-1 ring-gray-900/5">
                                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                </template>
                            </td>

                            {{-- Title --}}
                            <td class="px-6 py-4 max-w-xs">
                                <p class="text-sm font-semibold text-foreground line-clamp-2" x-text="book.title || '—'"></p>
                                <p class="mt-0.5 text-xs text-muted-foreground" x-text="book.isbn ? 'ISBN: ' + book.isbn : ''"></p>
                            </td>

                            {{-- Author --}}
                            <td class="px-6 py-4">
                                <p class="text-sm text-muted-foreground" x-text="book.author || '—'"></p>
                            </td>

                            {{-- Year --}}
                            <td class="px-6 py-4">
                                <p class="text-sm text-muted-foreground" x-text="book.published_year || '—'"></p>
                            </td>

                            {{-- Import Form --}}
                            <td class="px-6 py-4">
                                {{-- We use a standard HTML form with hidden inputs so the import
                                     goes through Laravel's backend validation cleanly. --}}
                                <form action="{{ route('admin.scraper.import') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="title"          :value="book.title">
                                    <input type="hidden" name="author"         :value="book.author">
                                    <input type="hidden" name="isbn"           :value="book.isbn">
                                    <input type="hidden" name="description"    :value="book.description">
                                    <input type="hidden" name="published_year" :value="book.published_year">
                                    <input type="hidden" name="cover_url"      :value="book.cover_url">
                                    <input type="hidden" name="work_key"       :value="book.work_key">
                                    <input type="hidden" name="subjects_json"  :value="JSON.stringify(book.subjects || [])">
                                    <button
                                        type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white transition bg-cyan-600 rounded-lg hover:bg-cyan-700 active:scale-95"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Import
                                    </button>
                                </form>
                            </td>

                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
