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
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 font-medium text-gray-400 transition rounded-lg hover:bg-gray-800 hover:text-white">
                    <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg> Dashboard
                </a>
                <a href="{{ route('admin.books.index') }}" class="flex items-center px-4 py-3 font-medium rounded-lg bg-cyan-600 text-cyan-50">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg> Manage Books
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-3 font-medium text-gray-400 transition rounded-lg hover:bg-gray-800 hover:text-white">
                    <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg> Orders
                </a>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="pt-10 pb-6 mt-auto">
                @csrf
                <button type="submit" class="flex items-center w-full px-4 py-3 font-medium text-left text-red-400 transition rounded-lg hover:bg-gray-800 hover:text-red-300"><svg class="w-5 h-5 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg> Log Out</button>
            </form>
        </nav>
    </aside>

    <main class="flex-1 min-w-0">
        <div class="flex items-center justify-between px-4 py-4 bg-white border-b border-gray-200 lg:hidden">
            <h2 class="text-xl font-bold text-gray-900"><span class="text-cyan-500">Shelf</span>-E Admin</h2>
            <button @click="sidebarOpen = true" class="p-2 -mr-2 text-gray-600 rounded-md hover:bg-gray-100"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg></button>
        </div>

        <div class="p-4 mx-auto sm:p-6 lg:p-8 max-w-7xl">
            
            <header class="flex flex-col gap-4 mb-8 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 sm:text-3xl">Manage Inventory</h1>
                    <p class="mt-1 text-sm text-gray-500">View, add, and manage the books in your store.</p>
                </div>
                <a href="{{ route('admin.books.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white transition bg-orange-500 rounded-lg hover:bg-orange-600 shadow-sm shrink-0">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Add New Book
                </a>
            </header>

            @if(session('success'))
                <div class="p-4 mb-6 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                    <span class="font-medium">Success!</span> {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm rounded-2xl ring-1 ring-gray-900/5">
                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-4 py-4 text-xs font-semibold tracking-wider text-gray-500 uppercase sm:px-6">Book Info</th>
                                <th class="px-4 py-4 text-xs font-semibold tracking-wider text-gray-500 uppercase sm:px-6">Price</th>
                                <th class="px-4 py-4 text-xs font-semibold tracking-wider text-gray-500 uppercase sm:px-6">Stock</th>
                                <th class="px-4 py-4 text-xs font-semibold tracking-wider text-gray-500 uppercase sm:px-6">Added On</th>
                                <th class="px-4 py-4 text-xs font-semibold tracking-wider text-right text-gray-500 uppercase sm:px-6">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200/60">
                            @forelse($books as $book)
                            <tr class="transition duration-150 hover:bg-gray-50/50">
                                <td class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-10 overflow-hidden bg-gray-100 border border-gray-200 rounded-md h-14">
                                            @if($book->image_path)
                                                <img class="object-cover w-full h-full" src="{{ asset('storage/' . $book->image_path) }}" alt="{{ $book->title }}">
                                            @else
                                                <div class="flex items-center justify-center w-full h-full text-xs text-gray-400">No Img</div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900 truncate max-w-[200px] sm:max-w-xs" title="{{ $book->title }}">{{ $book->title }}</div>
                                            <div class="text-sm text-gray-500 truncate max-w-[200px] sm:max-w-xs">{{ $book->author }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 font-semibold text-gray-900 sm:px-6">৳ {{ number_format($book->price, 0) }}</td>
                                <td class="px-4 py-4 sm:px-6">
                                    @if($book->stock_quantity > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20">
                                            {{ $book->stock_quantity }} in stock
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10">
                                            Out of Stock
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500 sm:px-6">{{ $book->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-4 text-sm font-medium text-right sm:px-6">
                                    <div class="flex items-center justify-end space-x-3">
                                        <a href="{{ route('admin.books.edit', $book->id) }}" class="font-medium transition text-cyan-600 hover:text-cyan-900">Edit</a>
                                        
                                        <form action="{{ route('admin.books.destroy', $book->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this book? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 transition hover:text-red-700">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-gray-500 sm:px-6">
                                    No books in your inventory yet. Click "Add New Book" to get started!
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $books->links() }}
            </div>

        </div>
    </main>
</div>
@endsection