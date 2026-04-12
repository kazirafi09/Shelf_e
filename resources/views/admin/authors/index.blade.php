@extends('layouts.admin')

@section('title', 'Manage Authors')
@section('subtitle', 'Create, edit, and manage book authors in your catalog.')

@section('admin-content')

@if(session('success'))
    <div class="p-4 mb-6 text-sm font-bold text-green-700 bg-green-100 border border-green-200 rounded-xl">
        {{ session('success') }}
    </div>
@endif

{{-- Search + Create Bar --}}
<div class="flex flex-col items-start justify-between gap-4 mb-6 sm:flex-row sm:items-center">
    <form method="GET" action="{{ route('admin.authors.index') }}" class="flex flex-1 gap-2 sm:max-w-xs">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search authors..."
               class="block w-full px-4 py-2.5 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm">
        <button type="submit"
                class="inline-flex items-center px-4 py-2.5 text-sm font-bold transition bg-primary text-primary-foreground hover:bg-primary/90 rounded-lg active:scale-95 shrink-0">
            Search
        </button>
        @if(request('search'))
            <a href="{{ route('admin.authors.index') }}"
               class="inline-flex items-center px-4 py-2.5 text-sm font-bold text-foreground transition bg-muted rounded-lg hover:opacity-80 shrink-0">
                Clear
            </a>
        @endif
    </form>

    <a href="{{ route('admin.authors.create') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold transition bg-primary text-primary-foreground hover:bg-primary/90 rounded-xl active:scale-95 shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Author
    </a>
</div>

<div class="overflow-hidden bg-card text-card-foreground border border-border shadow-sm rounded-2xl">
    <table class="w-full text-left">
        <thead class="bg-muted">
            <tr>
                <th class="px-6 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase">Author</th>
                <th class="px-6 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase">Biography</th>
                <th class="px-6 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-border">
            @forelse($authors as $author)
            <tr class="transition hover:bg-muted/50">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 overflow-hidden bg-muted rounded-full shrink-0">
                            @if($author->photo_path)
                                <img src="{{ asset('storage/' . $author->photo_path) }}"
                                     alt="{{ $author->name }}"
                                     class="object-cover w-full h-full">
                            @else
                                <div class="flex items-center justify-center w-full h-full text-muted-foreground">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-foreground">{{ $author->name }}</p>
                            <p class="text-xs text-muted-foreground">{{ $author->book_count ?? 0 }} book(s)</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm text-muted-foreground max-w-xs">
                    <p class="line-clamp-2">{{ $author->bio ?? '—' }}</p>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.authors.edit', $author) }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-cyan-700 bg-cyan-50 hover:bg-cyan-100 border border-cyan-200 rounded-lg transition-colors">
                            Edit
                        </a>
                        <form action="{{ route('admin.authors.destroy', $author) }}" method="POST"
                              onsubmit="return confirm('Delete {{ addslashes($author->name) }}? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold bg-destructive text-destructive-foreground hover:bg-destructive/90 rounded-lg transition-colors">
                                Delete
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="px-6 py-16 text-sm font-medium text-center text-muted-foreground">
                    No authors found.
                    <a href="{{ route('admin.authors.create') }}"
                       class="block mt-1 font-bold text-cyan-600 hover:underline">
                        Add the first author &rarr;
                    </a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-8">
    {{ $authors->links() }}
</div>

@endsection
