@extends('layouts.admin')

@section('title', 'Contact Messages')
@section('subtitle', 'Messages submitted through the contact form.')

@section('admin-content')

@if(session('success'))
    <div class="p-4 mb-6 text-sm font-bold text-green-700 bg-green-100 border border-green-200 rounded-xl">
        {{ session('success') }}
    </div>
@endif

<div class="space-y-4">
    @forelse($messages as $message)
        <div
            x-data="{ open: false }"
            class="bg-background border rounded-xl shadow-sm overflow-hidden {{ $message->is_read ? 'border-border opacity-70' : 'border-cyan-400' }}"
        >
            {{-- Header row --}}
            <div class="flex items-center justify-between px-5 py-4 cursor-pointer" @click="open = !open">
                <div class="flex items-center gap-3 min-w-0">
                    @if(!$message->is_read)
                        <span class="inline-flex w-2 h-2 rounded-full bg-cyan-500 shrink-0"></span>
                    @else
                        <span class="inline-flex w-2 h-2 rounded-full bg-gray-300 shrink-0"></span>
                    @endif
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-foreground truncate">{{ $message->name }}</p>
                        <p class="text-xs text-muted-foreground truncate">{{ $message->email }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 shrink-0 ml-4">
                    <span class="hidden sm:block text-xs text-muted-foreground">{{ $message->created_at->format('d M Y, g:i a') }}</span>
                    <svg class="w-4 h-4 text-muted-foreground transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>

            {{-- Expandable body --}}
            <div x-show="open" x-collapse class="border-t border-border">
                <div class="px-5 py-4 space-y-4">
                    <p class="text-sm text-foreground whitespace-pre-wrap">{{ $message->message }}</p>
                    <div class="flex items-center gap-3 pt-2">
                        @if(!$message->is_read)
                            <form method="POST" action="{{ route('admin.contacts.markRead', $message) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-cyan-700 bg-cyan-50 border border-cyan-200 rounded-lg hover:bg-cyan-100 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Mark as Read
                                </button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('admin.contacts.destroy', $message) }}"
                              onsubmit="return confirm('Delete this message?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="py-20 text-center text-muted-foreground">
            <svg class="w-12 h-12 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <p class="text-sm font-medium">No messages yet.</p>
        </div>
    @endforelse
</div>

@if($messages->hasPages())
    <div class="mt-8">{{ $messages->links() }}</div>
@endif

@endsection
