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
          class="p-8 bg-white border border-gray-100 shadow-sm rounded-3xl">
        @csrf

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            
            <div class="md:col-span-2">
                <label for="title" class="block mb-1 text-sm font-bold text-gray-900">Book Title</label>
                <input type="text" id="title" name="title" placeholder="e.g. The Great Gatsby" required
                    class="block w-full px-4 py-3 text-sm placeholder-gray-400 transition-all border-gray-200 rounded-lg shadow-sm bg-gray-50 focus:border-cyan-500 focus:ring-cyan-500">
            </div>

            <div>
                <label for="author" class="block mb-1 text-sm font-bold text-gray-900">Author</label>
                <input type="text" id="author" name="author" placeholder="e.g. F. Scott Fitzgerald" required
                    class="block w-full px-4 py-3 text-sm placeholder-gray-400 transition-all border-gray-200 rounded-lg shadow-sm bg-gray-50 focus:border-cyan-500 focus:ring-cyan-500">
            </div>

            <div>
                <label for="category" class="block mb-1 text-sm font-bold text-gray-900">Category</label>
                <select id="category" name="category_id" class="block w-full px-4 py-3 text-sm text-gray-600 transition-all border-gray-200 rounded-lg shadow-sm bg-gray-50 focus:border-cyan-500 focus:ring-cyan-500">
                    <option value="">Select a Category...</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="paperback_price" class="block mb-1 text-sm font-bold text-gray-900">Paperback Price (৳)</label>
                    <input type="number" id="paperback_price" name="paperback_price" step="0.01" placeholder="0.00"
                        class="block w-full px-4 py-3 text-sm placeholder-gray-400 transition-all border-gray-200 rounded-lg shadow-sm bg-gray-50 focus:border-cyan-500 focus:ring-cyan-500">
                    <p class="mt-1 text-xs text-gray-500">Leave empty if not available</p>
                </div>
                <div>
                    <label for="hardcover_price" class="block mb-1 text-sm font-bold text-gray-900">Hardcover Price (৳)</label>
                    <input type="number" id="hardcover_price" name="hardcover_price" step="0.01" placeholder="0.00"
                        class="block w-full px-4 py-3 text-sm placeholder-gray-400 transition-all border-gray-200 rounded-lg shadow-sm bg-gray-50 focus:border-cyan-500 focus:ring-cyan-500">
                    <p class="mt-1 text-xs text-gray-500">Leave empty if not available</p>
                </div>
            </div>
            <p class="mt-2 text-xs text-orange-600">* At least one price format is required</p>

            <div>
                <label for="stock" class="block mb-1 text-sm font-bold text-gray-900">Stock Quantity</label>
                <input type="number" id="stock" name="stock_quantity" placeholder="How many in stock?" required
                    class="block w-full px-4 py-3 text-sm placeholder-gray-400 transition-all border-gray-200 rounded-lg shadow-sm bg-gray-50 focus:border-cyan-500 focus:ring-cyan-500">
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block mb-1 text-sm font-bold text-gray-900">Description</label>
                <textarea id="description" name="description" rows="4" placeholder="Write a short summary of the book..." required
                    class="block w-full px-4 py-3 text-sm placeholder-gray-400 transition-all border-gray-200 rounded-lg shadow-sm bg-gray-50 focus:border-cyan-500 focus:ring-cyan-500"></textarea>
            </div>

            <div class="md:col-span-2">
                <label for="synopsis" class="block mb-1 text-sm font-bold text-gray-900">Synopsis</label>
                <textarea id="synopsis" name="synopsis" rows="4" placeholder="Write a brief synopsis of the book..." class="block w-full px-4 py-3 text-sm placeholder-gray-400 transition-all border-gray-200 rounded-lg shadow-sm bg-gray-50 focus:border-cyan-500 focus:ring-cyan-500"></textarea>
            </div>

            <div class="mb-6">
                <label class="block mb-2 text-sm font-bold text-gray-900">Cover Image</label>
                
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
                            reader.onload = e => this.imageUrl = e.target.result;
                        }
                    }"
                    @dragover.prevent="isDragging = true"
                    @dragleave.prevent="isDragging = false"
                    @drop.prevent="handleDrop($event)"
                    :class="isDragging ? 'border-cyan-500 bg-cyan-50' : 'border-gray-300 bg-white'"
                    class="relative flex flex-col items-center justify-center w-full p-6 overflow-hidden transition-colors border-2 border-dashed rounded-xl h-52 group hover:bg-gray-50">

                    <input x-ref="fileInput" type="file" name="image" @change="fileChosen" accept="image/png, image/jpeg, image/webp" class="absolute inset-0 z-50 w-full h-full opacity-0 cursor-pointer">

                    <div x-show="!imageUrl" class="text-center text-gray-500 transition-transform pointer-events-none group-hover:scale-105">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="text-sm font-medium text-gray-700"><span class="text-cyan-600">Click to upload</span> or drag and drop</p>
                        <p class="mt-1 text-xs text-gray-400">PNG, JPG, WEBP up to 2MB</p>
                    </div>

                    <div x-show="imageUrl" style="display: none;" class="absolute inset-0 z-40 flex items-center justify-center w-full h-full p-2 bg-gray-50">
                        <img :src="imageUrl" class="object-contain w-full h-full rounded-lg shadow-sm">
                        <div class="absolute inset-0 flex items-center justify-center transition-opacity opacity-0 bg-black/40 group-hover:opacity-100 rounded-xl">
                            <span class="px-4 py-2 text-sm font-medium text-white rounded-lg bg-black/60">Click or Drop to change</span>
                        </div>
                    </div>

                </div>
                @error('image') <span class="block mt-1 text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

        </div>

        <div class="flex items-center justify-end gap-4 pt-6 mt-8 border-t border-gray-100">
             <a href="{{ route('admin.books.index') }}" class="px-6 py-3 text-sm font-bold text-gray-500 transition-colors hover:text-gray-700">
                Discard Changes
            </a>
            <button type="submit" class="px-10 py-4 text-sm font-bold text-white transition rounded-full shadow-lg bg-cyan-500 hover:bg-cyan-600 hover:shadow-cyan-500/30 active:scale-95">
                Save Book to Store
            </button>
        </div>

    </form>
</div>
@endsection