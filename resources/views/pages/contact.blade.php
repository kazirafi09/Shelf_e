@extends('layouts.app')

@section('content')
<div class="container px-4 py-12 mx-auto max-w-7xl">
    <div class="grid grid-cols-1 gap-12 lg:grid-cols-2">
        
        <div>
            <h1 class="mb-4 text-4xl font-extrabold text-foreground">Get in Touch</h1>
            <p class="mb-8 text-lg text-muted-foreground">Have a question about an order, a specific book, or just want to say hello? We'd love to hear from you.</p>
            
            <div class="space-y-6">
                <div class="flex items-start p-6 bg-card text-card-foreground border border-border shadow-sm rounded-2xl">
                    <div class="p-3 rounded-full bg-cyan-50 text-cyan-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    </div>
                    <div class="ml-5">
                        <h4 class="text-lg font-bold text-foreground">Phone Support</h4>
                        <p class="mt-1 text-muted-foreground">+880 1234 567890</p>
                        <p class="text-sm text-muted-foreground">Sun-Thu, 9am - 6pm</p>
                    </div>
                </div>

                <div class="flex items-start p-6 bg-card text-card-foreground border border-border shadow-sm rounded-2xl">
                    <div class="p-3 text-orange-500 rounded-full bg-orange-50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="ml-5">
                        <h4 class="text-lg font-bold text-foreground">Email Us</h4>
                        <p class="mt-1 text-muted-foreground">support@shelf-e.com</p>
                        <p class="text-sm text-muted-foreground">We aim to reply within 24 hours.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-8 bg-card text-card-foreground border border-border shadow-xl rounded-3xl">
            <h3 class="mb-6 text-2xl font-bold text-foreground">Send a Message</h3>
            
            {{-- Success Flash Message --}}
            @if(session('success'))
                <div class="p-4 mb-6 text-sm text-green-700 border border-green-200 bg-green-50 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Updated Form --}}
            <form action="{{ route('contact.store') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block mb-1 text-sm font-medium text-foreground">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-3 bg-background border @error('name') border-red-500 @else border-input @enderror rounded-xl focus:ring-2 focus:ring-ring focus:outline-none text-foreground" required>
                    @error('name') <span class="mt-1 text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium text-foreground">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-3 bg-background border @error('email') border-red-500 @else border-input @enderror rounded-xl focus:ring-2 focus:ring-ring focus:outline-none text-foreground" required>
                    @error('email') <span class="mt-1 text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium text-foreground">Message</label>
                    <textarea name="message" rows="4" class="w-full px-4 py-3 bg-background border @error('message') border-red-500 @else border-input @enderror rounded-xl focus:ring-2 focus:ring-ring focus:outline-none text-foreground" required>{{ old('message') }}</textarea>
                    @error('message') <span class="mt-1 text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="w-full py-3.5 text-base font-bold text-white transition-colors bg-cyan-600 rounded-xl hover:bg-cyan-700 shadow-md">
                    Send Message
                </button>
            </form>
        </div>

    </div>
</div>
@endsection