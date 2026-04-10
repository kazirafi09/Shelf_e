@extends('layouts.admin')

{{-- 1. Set the Header Text --}}
@section('title', 'Add New Book')
@section('subtitle', 'Fill in the details to add a new book to your store inventory.')

@section('admin-content')
<div class="max-w-4xl mx-auto">
    
    {{-- Optional: Success/Error Messages --}}
    @if ($errors->any())
        <div class="p-4 mb-6 text-sm text-red-700 bg-red-100 border border-red-200 rounded-xl">
            <ul class="pl-5 list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- The Form --}}
    <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data"
          class="p-8 bg-card text-card-foreground border border-border shadow-sm rounded-3xl">
        @csrf

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            
            <div class="md:col-span-2">
                <label for="title" class="block mb-1 text-sm font-bold text-foreground">Book Title</label>
                <input type="text" id="title" name="title" placeholder="e.g. The Great Gatsby" required
                    value="{{ old('title') }}"
                    class="block w-full px-4 py-3 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm transition-all">
            </div>

            @php
                $oldAuthorIds = old('author_ids', []);
                $oldAuthors = $oldAuthorIds
                    ? \App\Models\Author::whereIn('id', $oldAuthorIds)->get()->map(fn($a) => ['id' => $a->id, 'name' => $a->name])->values()
                    : collect();
            @endphp
            <div
                x-data="{
                    query: '',
                    results: [],
                    selectedAuthors: @json($oldAuthors),
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
                <label class="block mb-1 text-sm font-bold text-foreground">Authors</label>

                {{-- Hidden legacy author string (keeps controller validation happy) --}}
                <input type="hidden" name="author" :value="selectedAuthors.map(a => a.name).join(', ') || 'Unknown'">

                {{-- Hidden author_ids[] for the pivot relationship --}}
                <template x-for="author in selectedAuthors" :key="author.id">
                    <input type="hidden" name="author_ids[]" :value="author.id">
                </template>

                {{-- Selected chips --}}
                <div class="flex flex-wrap gap-1.5 mb-2" x-show="selectedAuthors.length > 0" style="display: none;">
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
                    class="block w-full px-4 py-3 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm transition-all"
                >

                {{-- Dropdown --}}
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="absolute z-20 w-full mt-1 overflow-hidden bg-background border border-border rounded-xl shadow-lg"
                    style="display: none;"
                >
                    <template x-for="author in results" :key="author.id">
                        <button
                            type="button"
                            @click="select(author)"
                            class="flex items-center w-full gap-3 px-4 py-2.5 text-sm text-left text-foreground hover:bg-cyan-50 hover:text-cyan-700 transition-colors"
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
                <label for="category" class="block mb-1 text-sm font-bold text-foreground">Category</label>
                <select id="category" name="category_id" class="block w-full px-4 py-3 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm transition-all">
                    <option value="">Select a Category...</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="paperback_price" class="block mb-1 text-sm font-bold text-foreground">Paperback Price (৳)</label>
                    <input type="number" id="paperback_price" name="paperback_price" step="0.01" placeholder="0.00"
                        value="{{ old('paperback_price') }}"
                        class="block w-full px-4 py-3 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm transition-all">
                    <p class="mt-1 text-xs text-muted-foreground">Leave empty if not available</p>
                </div>
                <div>
                    <label for="hardcover_price" class="block mb-1 text-sm font-bold text-foreground">Hardcover Price (৳)</label>
                    <input type="number" id="hardcover_price" name="hardcover_price" step="0.01" placeholder="0.00"
                        value="{{ old('hardcover_price') }}"
                        class="block w-full px-4 py-3 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm transition-all">
                    <p class="mt-1 text-xs text-muted-foreground">Leave empty if not available</p>
                </div>
            </div>
            <p class="mt-2 text-xs text-orange-600">* At least one price format is required</p>

            {{-- Sale & Promotion --}}
            <div class="md:col-span-2 p-4 border border-border rounded-xl bg-muted/30">
                <p class="mb-3 text-sm font-bold text-foreground">Sale & Promotion <span class="text-xs font-normal text-muted-foreground">(optional)</span></p>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="sale_price" class="block mb-1 text-sm font-medium text-foreground">Sale Price (৳)</label>
                        <input type="number" id="sale_price" name="sale_price" step="0.01" placeholder="0.00"
                            value="{{ old('sale_price') }}"
                            class="block w-full px-4 py-3 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm transition-all">
                        <p class="mt-1 text-xs text-muted-foreground">Discounted price — overrides format prices when sale is active</p>
                    </div>
                    <div>
                        <label for="sale_ends_at" class="block mb-1 text-sm font-medium text-foreground">Sale Ends At</label>
                        <input type="datetime-local" id="sale_ends_at" name="sale_ends_at"
                            value="{{ old('sale_ends_at') }}"
                            class="block w-full px-4 py-3 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm transition-all">
                        <p class="mt-1 text-xs text-muted-foreground">Leave blank to run the sale indefinitely</p>
                    </div>
                </div>
            </div>

            <div>
                <label for="stock" class="block mb-1 text-sm font-bold text-foreground">Stock Quantity</label>
                <input type="number" id="stock" name="stock_quantity" placeholder="How many in stock?" required
                    value="{{ old('stock_quantity') }}"
                    class="block w-full px-4 py-3 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm transition-all">
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block mb-1 text-sm font-bold text-foreground">Description</label>
                <textarea id="description" name="description" rows="4" placeholder="Write a short summary of the book..." required
                    class="block w-full px-4 py-3 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm transition-all">{{ old('description') }}</textarea>
            </div>

            <div class="md:col-span-2">
                <label for="synopsis" class="block mb-1 text-sm font-bold text-foreground">Synopsis</label>
                <textarea id="synopsis" name="synopsis" rows="4" placeholder="Write a brief synopsis of the book..." class="block w-full px-4 py-3 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm transition-all">{{ old('synopsis') }}</textarea>
            </div>

            <div class="mb-6">
                <label class="block mb-2 text-sm font-bold text-foreground">Cover Image</label>
                
                <div x-data="{ 
                        imageUrl: null,
                        isDragging: false,
                        fileChosen(event) {
                            this.processFile(event.target.files[0]);
                        },
                        handleDrop(event) {
                            this.isDragging = false;
                            const file = event.dataTransfer.files[0];
                            this.processFile(file);
                            
                            // Sync the dropped file back to the hidden HTML input so the form submits it
                            this.$refs.fileInput.files = event.dataTransfer.files;
                        },
                        processFile(file) {
                            if (!file || !file.type.match('image.*')) return;
                            const reader = new FileReader();
                            reader.readAsDataURL(file);
                            reader.onload = function(e) { this.imageUrl = e.target.result; }.bind(this);
                        }
                    }"
                    @dragover.prevent="isDragging = true"
                    @dragleave.prevent="if (!$el.contains($event.relatedTarget)) isDragging = false"
                    @drop.prevent="handleDrop($event)"
                    @click="$refs.fileInput.click()"
                    :class="isDragging ? 'border-cyan-500 bg-cyan-50' : 'border-border bg-background'"
                    class="relative flex flex-col items-center justify-center w-full p-6 overflow-hidden transition-colors border-2 border-dashed rounded-xl h-52 group hover:bg-muted cursor-pointer">

                    <input x-ref="fileInput" type="file" name="image" @change="fileChosen" accept="image/png, image/jpeg, image/webp" class="hidden">

                    <div x-show="!imageUrl" class="text-center text-muted-foreground transition-transform pointer-events-none group-hover:scale-105">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="text-sm font-medium text-foreground"><span class="text-cyan-600">Click to upload</span> or drag and drop</p>
                        <p class="mt-1 text-xs text-muted-foreground">PNG, JPG, WEBP up to 2MB</p>
                    </div>

                    <div x-show="imageUrl" style="display: none;" @click.stop="$refs.fileInput.click()" class="absolute inset-0 z-40 flex items-center justify-center w-full h-full p-2 bg-muted cursor-pointer">
                        <img :src="imageUrl" class="object-contain w-full h-full rounded-lg shadow-sm">
                        <div class="absolute inset-0 flex items-center justify-center transition-opacity opacity-0 bg-black/40 group-hover:opacity-100 rounded-xl">
                            <span class="px-4 py-2 text-sm font-medium text-white rounded-lg bg-black/60">Click or Drop to change</span>
                        </div>
                    </div>

                </div>
                @error('image') <span class="block mt-1 text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

        </div>

        <div class="flex items-center justify-end gap-4 pt-6 mt-8 border-t border-border">
             <a href="{{ route('admin.books.index') }}" class="px-6 py-3 text-sm font-bold text-muted-foreground transition-colors hover:text-foreground">
                Discard Changes
            </a>
            <button type="submit" class="px-10 py-4 text-sm font-bold text-white transition rounded-full shadow-lg bg-cyan-500 hover:bg-cyan-600 hover:shadow-cyan-500/30 active:scale-95">
                Save Book to Store
            </button>
        </div>

    </form>
</div>
@endsection