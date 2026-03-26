@extends('layouts.app')

@section('content')
<div class="container px-4 py-12 mx-auto">
    
    <div class="mb-6 text-sm text-gray-500">
        <a href="/" class="transition-colors hover:text-orange-500">Home</a> 
        <span class="mx-2">&gt;</span> 
        <span class="font-medium text-gray-900">Authors</span>
    </div>

    <div class="flex items-center justify-between mb-10">
        <h1 class="text-3xl font-bold text-gray-900 md:text-4xl">Meet Our Authors</h1>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
        @foreach($authors as $author)
            <a href="{{ route('categories.index', ['authors[]' => $author->author]) }}" class="flex items-center p-6 transition-all duration-300 bg-white border border-gray-100 shadow-sm rounded-2xl hover:shadow-lg hover:-translate-y-1 hover:border-cyan-200 group">
                <div class="flex items-center justify-center w-16 h-16 text-2xl font-black text-white transition-colors duration-300 rounded-full shadow-inner shrink-0 bg-cyan-500 group-hover:bg-cyan-600">
                    {{ substr($author->author, 0, 1) }}
                </div>
                <div class="ml-5">
                    <h3 class="text-lg font-bold text-gray-900 transition-colors group-hover:text-cyan-600">{{ $author->author }}</h3>
                    <p class="mt-1 text-sm font-medium text-orange-500">{{ $author->book_count }} {{ Str::plural('Book', $author->book_count) }}</p>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-12">
        {{ $authors->links() }}
    </div>
</div>
@endsection