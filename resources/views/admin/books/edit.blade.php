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

            <div>
                <label class="block text-sm font-bold text-gray-700">Author</label>
                <input type="text" name="author" value="{{ old('author', $book->author) }}" required 
                       class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm bg-gray-50/50">
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

            <div>
                <label class="block text-sm font-bold text-gray-700">Price (৳)</label>
                <input type="number" step="0.01" name="price" value="{{ old('price', $book->price) }}" required 
                       class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm bg-gray-50/50">
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
        <div class="flex items-center justify-end gap-4 pt-6 mt-8 border-t border-gray-100">
            <a href="{{ route('admin.books.index') }}" class="text-sm font-bold text-gray-400 transition-colors hover:text-gray-600">
                Cancel Changes
            </a>
            <button type="submit" 
                    class="px-8 py-3 text-sm font-bold text-white transition rounded-full shadow-lg bg-cyan-600 hover:bg-cyan-700 hover:shadow-cyan-600/30 active:scale-95">
                Update Book Details
            </button>
        </div>
    </form>
</div>
@endsection