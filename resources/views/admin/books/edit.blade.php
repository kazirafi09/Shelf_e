@extends('layouts.admin')

{{-- 1. Set the Dynamic Header --}}
@section('title', 'Edit Book')
@section('subtitle')
    Update details for <span class="font-bold text-foreground">"{{ $book->title }}"</span>.
@endsection

@section('admin-content')
<div class="max-w-4xl mx-auto">

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
          class="p-6 border shadow-sm bg-card text-card-foreground border-border rounded-2xl ring-1 ring-gray-900/5 sm:p-8">
        @csrf
        @method('PUT')
        <input type="hidden" name="page" value="{{ request('page', 1) }}">

        <div class="grid grid-cols-1 gap-y-6 gap-x-6 sm:grid-cols-2">
            
            <div class="sm:col-span-2">
                <label class="block text-sm font-bold text-foreground">Book Title</label>
                <input type="text" name="title" value="{{ old('title', $book->title) }}" required
                       class="block w-full mt-1 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm sm:text-sm">
            </div>

            <div
                x-data="{
                    query: '',
                    results: [],
                    selectedAuthors: {{ Illuminate\Support\Js::from($book->authors->map->only('id', 'name')) }},
                    open: false,
                    showModal: false,
                    newName: '',
                    saving: false,
                    errorMsg: null,
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
                    },
                    async saveAuthor() {
                        this.saving = true;
                        this.errorMsg = null;
                        try {
                            const res = await fetch('{{ route('admin.authors.store') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({ name: this.newName }),
                            });
                            const data = await res.json();
                            if (!res.ok) {
                                this.errorMsg = data.errors?.name?.[0] ?? 'Something went wrong.';
                            } else {
                                this.select({ id: data.id, name: data.name });
                                this.newName = '';
                                this.showModal = false;
                            }
                        } catch (e) {
                            this.errorMsg = 'Network error.';
                        } finally {
                            this.saving = false;
                        }
                    }
                }"
                class="relative"
            >
                <label class="block mb-1 text-sm font-bold text-foreground">Authors</label>

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

                {{-- Search input + Quick-Add button --}}
                <div class="flex gap-2">
                    <div class="relative flex-1">
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
                            class="absolute z-20 w-full mt-1 overflow-hidden border shadow-lg bg-background border-border rounded-xl"
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

                    <button type="button" @click="showModal = true"
                            title="Create new author"
                            class="flex items-center justify-center w-12 shrink-0 text-sm font-bold rounded-[var(--radius)] border border-input bg-muted text-foreground hover:bg-muted/80 transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                </div>

                {{-- Quick-Add Author Modal --}}
                <div
                    x-show="showModal"
                    x-transition.opacity
                    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
                    style="display: none;"
                    @keydown.escape.window="showModal = false"
                >
                    <div @click.outside="showModal = false" class="w-full max-w-sm p-6 bg-card border border-border rounded-2xl shadow-xl">
                        <h3 class="mb-1 text-sm font-bold text-foreground">Create New Author</h3>
                        <p class="mb-4 text-xs text-muted-foreground">They will be added and selected immediately.</p>
                        <input type="text" x-model="newName" @keydown.enter.prevent="saveAuthor()"
                               placeholder="Author name…"
                               class="block w-full px-4 py-2.5 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-xl shadow-sm mb-2">
                        <p x-show="errorMsg" x-text="errorMsg" class="mb-3 text-xs text-red-600" style="display: none;"></p>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showModal = false"
                                    class="px-4 py-2 text-sm font-medium text-muted-foreground hover:text-foreground transition">Cancel</button>
                            <button type="button" @click="saveAuthor()" :disabled="saving || newName.trim().length < 2"
                                    class="px-5 py-2 text-sm font-bold rounded-xl bg-primary text-primary-foreground hover:bg-primary/90 transition active:scale-95 disabled:opacity-50">
                                <span x-text="saving ? 'Saving…' : 'Create'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Category with inline Quick-Add --}}
            <div
                x-data="{
                    showModal: false,
                    newName: '',
                    saving: false,
                    errorMsg: null,
                    async save() {
                        this.saving = true;
                        this.errorMsg = null;
                        try {
                            const res = await fetch('{{ route('admin.categories.store') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({ name: this.newName }),
                            });
                            const data = await res.json();
                            if (!res.ok) {
                                this.errorMsg = data.errors?.name?.[0] ?? 'Something went wrong.';
                            } else {
                                const opt = new Option(data.name, data.id, true, true);
                                this.$refs.categorySelect.add(opt);
                                this.newName = '';
                                this.showModal = false;
                            }
                        } catch (e) {
                            this.errorMsg = 'Network error.';
                        } finally {
                            this.saving = false;
                        }
                    }
                }"
            >
                <label class="block text-sm font-bold text-foreground">Category</label>
                <div class="flex gap-2 mt-1">
                    <select x-ref="categorySelect" name="category_id" required
                            class="flex-1 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm sm:text-sm">
                        <option value="">Select Category...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" @click="showModal = true"
                            title="Create new category"
                            class="flex items-center justify-center w-10 shrink-0 text-sm font-bold rounded-[var(--radius)] border border-input bg-muted text-foreground hover:bg-muted/80 transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                </div>

                {{-- Quick-Add Modal --}}
                <div
                    x-show="showModal"
                    x-transition.opacity
                    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
                    style="display: none;"
                    @keydown.escape.window="showModal = false"
                >
                    <div @click.outside="showModal = false" class="w-full max-w-sm p-6 bg-card border border-border rounded-2xl shadow-xl">
                        <h3 class="mb-1 text-sm font-bold text-foreground">Create New Category</h3>
                        <p class="mb-4 text-xs text-muted-foreground">It will be added to the dropdown immediately.</p>
                        <input type="text" x-model="newName" @keydown.enter.prevent="save()"
                               placeholder="Category name…"
                               class="block w-full px-4 py-2.5 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-xl shadow-sm mb-2">
                        <p x-show="errorMsg" x-text="errorMsg" class="mb-3 text-xs text-red-600" style="display: none;"></p>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showModal = false"
                                    class="px-4 py-2 text-sm font-medium text-muted-foreground hover:text-foreground transition">Cancel</button>
                            <button type="button" @click="save()" :disabled="saving || newName.trim().length < 2"
                                    class="px-5 py-2 text-sm font-bold rounded-xl bg-primary text-primary-foreground hover:bg-primary/90 transition active:scale-95 disabled:opacity-50">
                                <span x-text="saving ? 'Saving…' : 'Create'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-foreground">Paperback Price (৳)</label>
                    <input type="number" step="0.01" name="paperback_price" value="{{ old('paperback_price', $book->paperback_price) }}"
                           class="block w-full mt-1 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-foreground">Hardcover Price (৳)</label>
                    <input type="number" step="0.01" name="hardcover_price" value="{{ old('hardcover_price', $book->hardcover_price) }}"
                           class="block w-full mt-1 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm sm:text-sm">
                </div>
            </div>

            {{-- Sale & Promotion --}}
            <div class="p-4 border sm:col-span-2 border-border rounded-xl bg-muted/30">
                <p class="mb-3 text-sm font-bold text-foreground">Sale & Promotion <span class="text-xs font-normal text-muted-foreground">(optional)</span></p>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-foreground">Sale Price (৳)</label>
                        <input type="number" name="sale_price" step="0.01" placeholder="0.00"
                            value="{{ old('sale_price', $book->sale_price) }}"
                            class="block w-full mt-1 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm sm:text-sm">
                        <p class="mt-1 text-xs text-muted-foreground">Discounted price — overrides format prices when sale is active</p>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-foreground">Sale Ends At</label>
                        <input type="datetime-local" name="sale_ends_at"
                            value="{{ old('sale_ends_at', $book->sale_ends_at ? $book->sale_ends_at->format('Y-m-d\TH:i') : '') }}"
                            class="block w-full mt-1 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm sm:text-sm">
                        <p class="mt-1 text-xs text-muted-foreground">Leave blank to run the sale indefinitely</p>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-foreground">Stock Quantity</label>
                <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $book->stock_quantity) }}" required
                       class="block w-full mt-1 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm sm:text-sm">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-bold text-foreground">Description</label>
                <textarea name="description" rows="4"
                          class="block w-full mt-1 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm sm:text-sm">{{ old('description', $book->description) }}</textarea>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-bold text-foreground">Synopsis</label>
                <textarea name="synopsis" rows="4"
                          class="block w-full mt-1 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm sm:text-sm">{{ old('synopsis', $book->synopsis) }}</textarea>
            </div>

            <div class="sm:col-span-2">
                <label class="block mb-2 text-sm font-bold text-foreground">Cover Image</label>
                <p class="mb-3 text-xs text-muted-foreground">Drag & drop a new image or click to browse. Leave unchanged to keep the current cover.</p>

                <div x-data="{
                        imageUrl: {{ Illuminate\Support\Js::from($book->image_path ? asset('storage/' . $book->image_path) : null) }},
                        isDragging: false,
                        fileChosen(event) {
                            this.processFile(event.target.files[0]);
                        },
                        handleDrop(event) {
                            this.isDragging = false;
                            const file = event.dataTransfer.files[0];
                            this.processFile(file);
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

                    <div x-show="!imageUrl" style="{{ $book->image_path ? 'display:none;' : '' }}" class="text-center transition-transform pointer-events-none text-muted-foreground group-hover:scale-105">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="text-sm font-medium text-foreground"><span class="text-cyan-600">Click to upload</span> or drag and drop</p>
                        <p class="mt-1 text-xs text-muted-foreground">PNG, JPG, WEBP up to 2MB</p>
                    </div>

                    <div x-show="imageUrl" style="{{ $book->image_path ? '' : 'display:none;' }}" @click.stop="$refs.fileInput.click()" class="absolute inset-0 z-40 flex items-center justify-center w-full h-full p-2 bg-muted cursor-pointer">
                        <img :src="imageUrl" src="{{ $book->image_path ? asset('storage/' . $book->image_path) : '' }}" class="object-contain w-full h-full rounded-lg shadow-sm">
                        <div class="absolute inset-0 flex items-center justify-center transition-opacity opacity-0 bg-black/40 group-hover:opacity-100 rounded-xl">
                            <span class="px-4 py-2 text-sm font-medium text-white rounded-lg bg-black/60">Click or Drop to change</span>
                        </div>
                    </div>
                </div>
                @error('image') <span class="block mt-1 text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

        </div>

        {{-- Footer Actions --}}
        <div class="flex items-center justify-between gap-4 pt-6 mt-6 border-t border-border">
            <a href="{{ route('product.show', $book->slug) }}" target="_blank"
               class="inline-flex items-center gap-1.5 text-sm font-medium transition-colors text-muted-foreground hover:text-foreground">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                View on Site
            </a>
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.books.index') }}" class="text-sm font-bold transition-colors text-muted-foreground hover:text-foreground">
                    Cancel Changes
                </a>
                <button type="submit"
                        class="px-8 py-3 text-sm font-bold text-white transition rounded-full shadow-lg bg-cyan-600 hover:bg-cyan-700 hover:shadow-cyan-600/30 active:scale-95">
                    Update Book Details
                </button>
            </div>
        </div>
    </form>

    {{-- ============================================================ --}}
    {{-- Peek Inside Media                                            --}}
    {{-- ============================================================ --}}
    <div class="p-6 mt-8 border shadow-sm bg-card text-card-foreground border-border rounded-2xl ring-1 ring-gray-900/5 sm:p-8">
        <div class="pb-4 mb-5 border-b border-border">
            <h3 class="text-base font-bold text-foreground">Peek Inside Media</h3>
            <p class="mt-0.5 text-sm text-muted-foreground">Upload images or short video clips shown on the product page as a preview.</p>
        </div>
        @include('admin.books.partials._preview-upload')
    </div>
</div>
@endsection