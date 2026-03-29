@extends('layouts.app')

@section('content')
<div class="container max-w-3xl px-4 py-16 mx-auto text-center">
    <div class="p-10 bg-white border border-gray-100 shadow-xl rounded-3xl">
        <div class="inline-flex items-center justify-center w-16 h-16 mb-6 rounded-full bg-cyan-50 text-cyan-600">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
        </div>
        
        <h1 class="mb-4 text-4xl font-extrabold text-gray-900">Join the Shelf-e Newsletter</h1>
        <p class="mb-8 text-lg text-gray-600">Get the latest updates on new book releases, author interviews, and exclusive subscriber discounts directly to your inbox.</p>
        
        @if(session('success'))
            <div class="p-4 mb-6 text-sm text-green-700 border border-green-200 bg-green-50 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('newsletter.subscribe') }}" method="POST" class="flex flex-col max-w-lg gap-3 mx-auto sm:flex-row sm:justify-center">
            @csrf
            <div class="flex-1 w-full">
                <input type="email" name="email" placeholder="Enter your email address" class="w-full px-4 py-3 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 bg-gray-50" required>
                @error('email') <span class="block mt-1 text-xs text-left text-red-500">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="px-6 py-3 font-bold text-white transition-colors shadow-md bg-cyan-600 rounded-xl hover:bg-cyan-700 whitespace-nowrap">
                Subscribe Now
            </button>
        </form>
    </div>
</div>
@endsection