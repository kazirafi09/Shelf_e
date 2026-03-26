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
                <label for="price" class="block mb-1 text-sm font-bold text-gray-900">Price (৳)</label>
                <input type="number" id="price" name="price" step="0.01" placeholder="0.00" required
                    class="block w-full px-4 py-3 text-sm placeholder-gray-400 transition-all border-gray-200 rounded-lg shadow-sm bg-gray-50 focus:border-cyan-500 focus:ring-cyan-500">
            </div>

            <div>
                <label for="category" class="block mb-1 text-sm font-bold text-gray-900">Category</label>
                <select id="category" name="category" class="block w-full px-4 py-3 text-sm text-gray-600 transition-all border-gray-200 rounded-lg shadow-sm bg-gray-50 focus:border-cyan-500 focus:ring-cyan-500">
                    <option value="">Select a Category...</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="stock" class="block mb-1 text-sm font-bold text-gray-900">Stock Quantity</label>
                <input type="number" id="stock" name="stock" placeholder="How many in stock?" required
                    class="block w-full px-4 py-3 text-sm placeholder-gray-400 transition-all border-gray-200 rounded-lg shadow-sm bg-gray-50 focus:border-cyan-500 focus:ring-cyan-500">
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block mb-1 text-sm font-bold text-gray-900">Description / Synopsis</label>
                <textarea id="description" name="description" rows="4" placeholder="Write a short summary of the book..." required
                    class="block w-full px-4 py-3 text-sm placeholder-gray-400 transition-all border-gray-200 rounded-lg shadow-sm bg-gray-50 focus:border-cyan-500 focus:ring-cyan-500"></textarea>
            </div>

            <div class="md:col-span-2">
                <label for="image" class="block mb-1 text-sm font-bold text-gray-900">Cover Image</label>
                <div class="relative flex items-center justify-center w-full px-6 py-10 transition-colors border-2 border-gray-300 border-dashed cursor-pointer rounded-xl bg-gray-50 hover:bg-gray-100 group">
                    <input type="file" id="image" name="image" accept="image/*" required class="absolute inset-0 opacity-0 cursor-pointer">
                    <div class="text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-400 transition-colors group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="mt-1 text-sm text-gray-600">Click to upload or drag and drop</p>
                        <p class="text-xs text-gray-500">PNG, JPG, WEBP up to 2MB</p>
                    </div>
                </div>
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