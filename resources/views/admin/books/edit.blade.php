@extends('layouts.admin')

@section('content')
<div class="flex min-h-screen font-sans bg-gray-50" x-data="{ sidebarOpen: false }">
    
    <div x-show="sidebarOpen" x-transition.opacity.duration.300ms @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-gray-900/80 backdrop-blur-sm lg:hidden" style="display: none;"></div>
    
    <aside class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 h-screen text-white transition-transform duration-300 ease-in-out transform bg-gray-900 shadow-2xl shrink-0 lg:translate-x-0 lg:sticky lg:top-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <div class="flex items-center justify-between p-6 shrink-0">
            <h2 class="text-2xl font-bold tracking-tight"><span class="text-cyan-400">Admin</span>Panel</h2>
            <button @click="sidebarOpen = false" class="text-gray-400 lg:hidden hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>
        <nav class="flex flex-col flex-1 px-4 mt-2 overflow-y-auto">
            <div class="space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 font-medium text-gray-400 transition rounded-lg hover:bg-gray-800 hover:text-white">Dashboard</a>
                <a href="{{ route('admin.books.index') }}" class="flex items-center px-4 py-3 font-medium rounded-lg bg-cyan-600 text-cyan-50">Manage Books</a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-3 font-medium text-gray-400 transition rounded-lg hover:bg-gray-800 hover:text-white">Orders</a>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="pt-10 pb-6 mt-auto">
                @csrf
                <button type="submit" class="flex items-center w-full px-4 py-3 font-medium text-left text-red-400 transition rounded-lg hover:bg-gray-800 hover:text-red-300">Log Out</button>
            </form>
        </nav>
    </aside>

    <main class="flex-1 min-w-0">
        <div class="flex items-center justify-between px-4 py-4 bg-white border-b border-gray-200 lg:hidden">
            <h2 class="text-xl font-bold text-gray-900"><span class="text-cyan-500">Shelf</span>-E Admin</h2>
            <button @click="sidebarOpen = true" class="p-2 -mr-2 text-gray-600 rounded-md hover:bg-gray-100"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg></button>
        </div>

        <div class="max-w-4xl p-4 mx-auto sm:p-6 lg:p-8">
            
            <header class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 sm:text-3xl">Edit Book</h1>
                    <p class="mt-1 text-sm text-gray-500">Update details for "{{ $book->title }}".</p>
                </div>
                <a href="{{ route('admin.books.index') }}" class="text-sm font-medium text-gray-500 transition hover:text-gray-900">&larr; Back to Inventory</a>
            </header>

            @if($errors->any())
                <div class="p-4 mb-6 text-sm text-red-700 border border-red-200 rounded-lg bg-red-50">
                    <ul class="pl-5 space-y-1 list-disc">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.books.update', $book->id) }}" method="POST" enctype="multipart/form-data" class="p-6 bg-white shadow-sm rounded-2xl ring-1 ring-gray-900/5 sm:p-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-y-6 gap-x-6 sm:grid-cols-2">
                    
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Book Title</label>
                        <input type="text" name="title" value="{{ old('title', $book->title) }}" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Author</label>
                        <input type="text" name="author" value="{{ old('author', $book->author) }}" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Category</label>
                        <select name="category_id" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm">
                            <option value="">Select Category...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Price (৳)</label>
                        <input type="number" step="0.01" name="price" value="{{ old('price', $book->price) }}" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Stock Quantity</label>
                        <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $book->stock_quantity) }}" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" rows="4" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm">{{ old('description', $book->description) }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Cover Image</label>
                        
                        <div class="flex items-start space-x-6">
                            @if($book->image_path)
                                <div class="shrink-0">
                                    <p class="mb-1 text-xs text-gray-500">Current Image</p>
                                    <img src="{{ asset('storage/' . $book->image_path) }}" alt="Current Cover" class="object-cover w-24 h-32 border border-gray-200 rounded-md">
                                </div>
                            @endif
                            
                            <div class="flex-1">
                                <p class="mb-1 text-xs text-gray-500">Upload New Image (Optional. Leave blank to keep current image)</p>
                                <input type="file" name="image" accept="image/*" class="block w-full text-sm text-gray-500 border border-gray-200 rounded-md file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="flex justify-end pt-6 mt-6 border-t border-gray-100">
                    <a href="{{ route('admin.books.index') }}" class="px-4 py-2 mr-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white border border-transparent rounded-md shadow-sm bg-cyan-600 hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                        Save Changes
                    </button>
                </div>
            </form>

        </div>
    </main>
</div>
@endsection