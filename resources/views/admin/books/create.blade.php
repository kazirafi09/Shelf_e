@extends('layouts.admin')

@section('content')
<div class="flex min-h-screen bg-gray-100">
    
    <aside class="hidden w-64 text-white bg-gray-900 md:block">
        <div class="p-6">
            <h2 class="text-2xl font-bold"><span class="text-cyan-400">Admin</span>Panel</h2>
        </div>
        <nav class="px-4 mt-6 space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-gray-400 transition rounded-lg hover:bg-gray-800 hover:text-white">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Dashboard
            </a>
            <a href="#" class="flex items-center px-4 py-3 font-bold rounded-lg bg-cyan-600">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Manage Books
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-gray-400 transition rounded-lg hover:bg-gray-800 hover:text-white">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                Orders
            </a>
        </nav>
    </aside>

    <main class="flex-1 p-8">
        <div class="max-w-4xl mx-auto">
            
            <header class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900">Add New Book</h1>
                    <p class="mt-1 text-sm text-gray-500">Fill in the details to add a new book to your store inventory.</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 text-sm font-bold text-gray-600 transition bg-white border border-gray-200 rounded-full shadow-sm hover:bg-gray-50 hover:text-gray-900">
                    Cancel & Go Back
                </a>
            </header>

            <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data" class="p-8 bg-white border border-gray-100 shadow-sm rounded-3xl">
                @csrf

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    
                    <div class="md:col-span-2">
                        <label for="title" class="block mb-1 text-sm font-bold text-gray-900">Book Title</label>
                        <input type="text" id="title" name="title" placeholder="e.g. The Great Gatsby" required
                            class="block w-full px-4 py-3 text-sm placeholder-gray-400 border-gray-200 rounded-lg shadow-sm bg-gray-50 focus:border-cyan-500 focus:ring-cyan-500">
                    </div>

                    <div>
                        <label for="author" class="block mb-1 text-sm font-bold text-gray-900">Author</label>
                        <input type="text" id="author" name="author" placeholder="e.g. F. Scott Fitzgerald" required
                            class="block w-full px-4 py-3 text-sm placeholder-gray-400 border-gray-200 rounded-lg shadow-sm bg-gray-50 focus:border-cyan-500 focus:ring-cyan-500">
                    </div>

                    <div>
                        <label for="price" class="block mb-1 text-sm font-bold text-gray-900">Price (৳)</label>
                        <input type="number" id="price" name="price" step="0.01" placeholder="0.00" required
                            class="block w-full px-4 py-3 text-sm placeholder-gray-400 border-gray-200 rounded-lg shadow-sm bg-gray-50 focus:border-cyan-500 focus:ring-cyan-500">
                    </div>

                    <div>
                        <label for="category" class="block mb-1 text-sm font-bold text-gray-900">Category</label>
                        <select id="category" name="category" class="block w-full px-4 py-3 text-sm text-gray-600 border-gray-200 rounded-lg shadow-sm bg-gray-50 focus:border-cyan-500 focus:ring-cyan-500">
                            <option value="">Select a Category...</option>
                            <option value="fiction">Fiction</option>
                            <option value="non-fiction">Non-Fiction</option>
                            <option value="science">Science & Tech</option>
                            <option value="history">History</option>
                        </select>
                    </div>

                    <div>
                        <label for="stock" class="block mb-1 text-sm font-bold text-gray-900">Stock Quantity</label>
                        <input type="number" id="stock" name="stock" placeholder="How many in stock?" required
                            class="block w-full px-4 py-3 text-sm placeholder-gray-400 border-gray-200 rounded-lg shadow-sm bg-gray-50 focus:border-cyan-500 focus:ring-cyan-500">
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block mb-1 text-sm font-bold text-gray-900">Description / Synopsis</label>
                        <textarea id="description" name="description" rows="4" placeholder="Write a short summary of the book..." required
                            class="block w-full px-4 py-3 text-sm placeholder-gray-400 border-gray-200 rounded-lg shadow-sm bg-gray-50 focus:border-cyan-500 focus:ring-cyan-500"></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label for="image" class="block mb-1 text-sm font-bold text-gray-900">Cover Image</label>
                        <input type="file" id="image" name="image" accept="image/*" required
                            class="block w-full px-4 py-3 text-sm border-gray-200 rounded-lg shadow-sm bg-gray-50 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                    </div>

                </div>

                <div class="pt-6 mt-8 border-t border-gray-100">
                    <button type="submit" class="w-full px-6 py-4 text-sm font-bold text-white transition rounded-full shadow-md bg-cyan-500 hover:bg-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 md:w-auto md:px-10">
                        Save Book to Store
                    </button>
                </div>

            </form>
        </div>
    </main>
</div>
@endsection