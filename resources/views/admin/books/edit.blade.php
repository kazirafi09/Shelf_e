@extends('layouts.admin')

{{-- 1. Set the Dynamic Header --}}
@section('title', 'Edit Book')
@section('subtitle')
    Update details for <span class="font-bold text-gray-900">"{{ $book->title }}"</span>.
@endsection

@section('admin-content')
<div class="max-w-4xl mx-auto">
    
    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="p-4 mb-6 text-sm text-red-700 border border-red-200 rounded-xl bg-red-50">
            <ul class="pl-5 space-y-1 list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- The Form --}}
    <form action="{{ route('admin.books.update', $book->id) }}" method="POST" enctype="multipart/form-data" 
          class="p-6 bg-white shadow-sm rounded-2xl ring-1 ring-gray-900/5 sm:p-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-y-6 gap-x-6 sm:grid-cols-2">
            
            <div class="sm:col-span-2">
                <label class="block text-sm font-bold text-gray-700">Book Title</label>
                <input type="text" name="title" value="{{ old('title', $book->title) }}" required 
                       class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm bg-gray-50/50">
            </div>

            <div
                x-data="{
                    query: '',
                    results: [],
                    selectedAuthors: @json($book->authors->map->only('id', 'name')),
                    open: false,
                    async search() {
                        if (this.query.length < 2) { this.results = []; this.open = false; return; }
                        const res = await fetch('/admin/authors/search?q=' + encodeURIComponent(this.query));
                        this.results = await res.json();
                        this.open = this.results.length > 0;
                    },
                    select(author) {
                        if (!this.selectedAuthors.find(a => a.id === author.id)) {
                            this.selectedAuthors.push({ id: author.id, name: author.name });
                        }
                        this.query = '';
                        this.results = [];
                        this.open = false;
                    },
                    remove(id) {
                        this.selectedAuthors = this.selectedAuthors.filter(a => a.id !== id);
                    }
                }"
                class="relative"
            >
                <label class="block mb-1 text-sm font-bold text-gray-700">Authors</label>

                {{-- Hidden legacy author string --}}
                <input type="hidden" name="author" :value="selectedAuthors.map(a => a.name).join(', ') || '{{ old('author', $book->author) }}'">

                {{-- Hidden author_ids[] for the pivot relationship --}}
                <template x-for="author in selectedAuthors" :key="author.id">
                    <input type="hidden" name="author_ids[]" :value="author.id">
                </template>

                {{-- Selected chips --}}
                <div class="flex flex-wrap gap-1.5 mb-2" x-show="selectedAuthors.length > 0">
                    <template x-for="author in selectedAuthors" :key="author.id">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-cyan-800 bg-cyan-100 border border-cyan-200 rounded-full">
                            <span x-text="author.name"></span>
                            <button type="button" @click="remove(author.id)"
                                    class="flex items-center justify-center w-3.5 h-3.5 rounded-full text-cyan-600 hover:text-red-600 hover:bg-red-100 transition-colors">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-2.5 h-2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </span>
                    </template>
                </div>

                {{-- Search input --}}
                <input
                    type="text"
                    x-model="query"
                    @input.debounce.300ms="search()"
                    @keydown.escape="open = false"
                    @click.outside="open = false"
                    placeholder="Search authors by name…"
                    autocomplete="off"
                    class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm bg-gray-50/50"
                >

                {{-- Dropdown --}}
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="absolute z-20 w-full mt-1 overflow-hidden bg-white border border-gray-200 rounded-xl shadow-lg"
                    style="display: none;"
                >
                    <template x-for="author in results" :key="author.id">
                        <button
                            type="button"
                            @click="select(author)"
                            class="flex items-center w-full gap-3 px-4 py-2.5 text-sm text-left text-gray-700 hover:bg-cyan-50 hover:text-cyan-700 transition-colors"
                        >
                            <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span x-text="author.name"></span>
                        </button>
                    </template>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700">Category</label>
                <select name="category_id" required 
                        class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm bg-gray-50/50">
                    <option value="">Select Category...</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700">Paperback Price (৳)</label>
                    <input type="number" step="0.01" name="paperback_price" value="{{ old('paperback_price', $book->paperback_price) }}"
                           class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm bg-gray-50/50">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700">Hardcover Price (৳)</label>
                    <input type="number" step="0.01" name="hardcover_price" value="{{ old('hardcover_price', $book->hardcover_price) }}"
                           class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm bg-gray-50/50">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700">Stock Quantity</label>
                <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $book->stock_quantity) }}" required 
                       class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm bg-gray-50/50">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-bold text-gray-700">Description</label>
                <textarea name="description" rows="4" 
                          class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm bg-gray-50/50">{{ old('description', $book->description) }}</textarea>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-bold text-gray-700">Synopsis</label>
                <textarea name="synopsis" rows="4" 
                          class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm bg-gray-50/50">{{ old('synopsis', $book->synopsis) }}</textarea>
            </div>

            <div class="sm:col-span-2">
                <label class="block mb-2 text-sm font-bold text-gray-700">Cover Image</label>
                
                <div class="flex items-start p-4 space-x-6 border rounded-xl bg-gray-50/30">
                    @if($book->image_path)
                        <div class="text-center shrink-0">
                            <p class="mb-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Current Cover</p>
                            <img src="{{ asset('storage/' . $book->image_path) }}" alt="Current Cover" 
                                 class="object-cover w-24 h-32 border-2 border-white rounded-lg shadow-md">
                        </div>
                    @endif
                    
                    <div class="flex-1">
                        <p class="mb-2 text-xs font-medium text-gray-500">Upload New Image <span class="text-gray-400">(Leave blank to keep current)</span></p>
                        <input type="file" name="image" accept="image/*" 
                               class="block w-full text-sm text-gray-500 transition-all cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                    </div>
                </div>
            </div>

        </div>

        {{-- Footer Actions --}}
        <div class="flex items-center justify-end gap-4 pt-6 mt-6 border-t border-gray-100">
            <a href="{{ route('admin.books.index') }}" class="text-sm font-bold text-gray-400 transition-colors hover:text-gray-600">
                Cancel Changes
            </a>
            <button type="submit" 
                    class="px-8 py-3 text-sm font-bold text-white transition rounded-full shadow-lg bg-cyan-600 hover:bg-cyan-700 hover:shadow-cyan-600/30 active:scale-95">
                Update Book Details
            </button>
        </div>
    </form>

    {{-- ============================================================ --}}
    {{-- Peek Inside Media                                            --}}
    {{-- ============================================================ --}}
    <div class="mt-8 p-6 bg-white shadow-sm rounded-2xl ring-1 ring-gray-900/5 sm:p-8">
        <div class="mb-5 pb-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Peek Inside Media</h3>
            <p class="mt-0.5 text-sm text-gray-500">Upload images or short video clips shown on the product page as a preview.</p>
        </div>
        @include('admin.books.partials._preview-upload')
    </div>
</div>
@endsection