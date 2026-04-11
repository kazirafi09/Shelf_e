@extends('layouts.admin')

@section('title', 'Manage Inventory')
@section('subtitle', 'View, add, and manage the books in your store.')

@section('admin-content')
    {{-- Top Action Bar --}}
    <div class="flex flex-col gap-4 mb-8 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" action="{{ route('admin.books.index') }}" class="relative w-full max-w-sm">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by title or author..."
                   class="block w-full py-2.5 pl-10 pr-3 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-xl shadow-sm transition-all">
        </form>
        
        <a href="{{ route('admin.books.create') }}" 
           class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white transition bg-orange-500 shadow-lg rounded-xl hover:bg-orange-600 shadow-orange-500/20 active:scale-95 shrink-0">
            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Add New Book
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 mb-6 text-sm font-bold text-green-700 bg-green-100 border border-green-200 rounded-xl animate-pulse">
            <span class="mr-2">✨</span> {{ session('success') }}
        </div>
    @endif

    {{-- Inventory Table --}}
    <div class="overflow-hidden bg-card text-card-foreground border border-border shadow-sm rounded-2xl ring-1 ring-gray-900/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead class="bg-muted">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase">Book Info</th>
                        <th class="px-6 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase">Category</th>
                        <th class="px-6 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase">Price</th>
                        <th class="px-6 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase">Stock Status</th>
                        <th class="px-6 py-4 text-xs font-bold tracking-wider text-right text-muted-foreground uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-card divide-y divide-border">
                    @forelse($books as $book)
                    <tr class="transition duration-150 hover:bg-muted/30 group">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-12 h-16 overflow-hidden bg-muted border border-border rounded-lg shadow-sm">
                                    @if($book->image_path)
                                        <img class="object-cover w-full h-full" src="{{ asset('storage/' . $book->image_path) }}" alt="{{ $book->title }}">
                                    @else
                                        <div class="flex items-center justify-center w-full h-full text-[10px] text-muted-foreground uppercase font-bold text-center p-1">No Cover</div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-foreground transition-colors group-hover:text-cyan-600">{{ $book->title }}</div>
                                    <div class="text-xs text-muted-foreground">{{ $book->author }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($book->category)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-muted border border-border text-muted-foreground">
                                    {{ $book->category->name }}
                                </span>
                            @else
                                <span class="text-xs text-muted-foreground">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-foreground">
                            ৳ {{ number_format($book->display_price, 0) }}
                        </td>
                        <td class="px-6 py-4">
                            @if($book->stock_quantity > 10)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-green-50 text-green-700 border border-green-100">
                                    {{ $book->stock_quantity }} in stock
                                </span>
                            @elseif($book->stock_quantity > 0)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                    Low Stock ({{ $book->stock_quantity }})
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-red-50 text-red-700 border border-red-100">
                                    Out of Stock
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-right">
                            <div class="flex items-center justify-end space-x-4">
                                <a href="{{ route('admin.books.edit', $book->id) . '?page=' . $books->currentPage() }}"
                                   class="font-bold transition-colors text-cyan-600 hover:text-cyan-800">Edit</a>
                                
                                <form action="{{ route('admin.books.destroy', $book->id) }}" method="POST" 
                                      onsubmit="return confirm('Permanently delete this book?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 transition-colors hover:text-red-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 mb-3 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                <p class="font-medium text-muted-foreground">No books found in inventory.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $books->links() }}
    </div>
@endsection