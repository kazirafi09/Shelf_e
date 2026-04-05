@extends('layouts.app')

@section('content')
<div class="container px-4 py-12 mx-auto max-w-7xl">

    <!-- Breadcrumb -->
    <div class="flex items-center mb-8 text-sm text-muted-foreground">
        <a href="/" class="hover:text-gray-700">Home</a>
        <span class="mx-2">/</span>
        <span class="font-medium text-foreground">Authors</span>
    </div>

    <!-- Page Header -->
    <div class="flex flex-col items-start justify-between gap-6 mb-10 md:flex-row md:items-center">
        <div>
            <h1 class="text-4xl font-bold tracking-tight text-foreground">
                Discover Authors
            </h1>
            <p class="mt-2 text-muted-foreground">
                Explore writers behind the books you love.
            </p>
        </div>

        <!-- Search -->
        <form method="GET" action="{{ route('authors.index') }}" class="relative w-full md:w-80">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search authors..."
                class="w-full py-2 pl-10 pr-4 transition bg-background border border-input text-foreground rounded-lg focus:ring-2 focus:ring-ring focus:outline-none"
            >
            <button type="submit" class="absolute left-3 top-2.5 text-gray-400 hover:text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.3-4.3m1.3-5.2a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </form>
    </div>

    <!-- Authors Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">

        @foreach($authors as $author)

        <a href="{{ route('categories.index', ['authors[]' => $author->author]) }}"
           class="relative p-6 transition-all duration-300 bg-card text-card-foreground border border-border shadow-sm group rounded-2xl hover:shadow-xl hover:-translate-y-1">

            <!-- Avatar -->
            <div class="w-32 h-32 mb-4 overflow-hidden rounded-full shadow-md shrink-0">
                @if($author->photo_path)
                    <img src="{{ asset('storage/' . $author->photo_path) }}"
                         alt="{{ $author->author }}"
                         class="object-cover w-full h-full">
                @else
                    <div class="flex items-center justify-center w-full h-full text-3xl font-bold text-white bg-gradient-to-br from-gray-500 to-blue-500">
                        {{ strtoupper(substr($author->author, 0, 1)) }}
                    </div>
                @endif
            </div>

            <!-- Author Name -->
            <h3 class="text-lg font-bold text-foreground transition-colors group-hover:text-gray-700">
                {{ $author->author }}
            </h3>

            <!-- Book Count -->
            <p class="mt-1 text-sm text-muted-foreground">
                {{ $author->book_count }} {{ Str::plural('Book', $author->book_count) }}
            </p>

            <!-- Hover CTA -->
            <div class="absolute text-sm font-semibold transition-opacity opacity-0 text-gray-700 bottom-5 right-6 group-hover:opacity-100">
                View Books →
            </div>

        </a>

        @endforeach

    </div>

    <!-- Pagination -->
    <div class="mt-14">
        {{ $authors->links('vendor.pagination.custom') }}
    </div>

</div>
@endsection