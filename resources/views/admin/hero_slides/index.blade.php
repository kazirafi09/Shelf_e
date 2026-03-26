@extends('layouts.admin')

{{-- 1. Set the Admin Header Titles --}}
@section('title', 'Carousel Management')
@section('subtitle', 'Update the images and text for your homepage 3D slider.')

@section('admin-content')
<div class="max-w-6xl mx-auto">
    
    {{-- Top Action Bar --}}
    <div class="flex items-center justify-end mb-8">
        <a href="{{ route('admin.hero-slides.create') }}" 
           class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white transition-all shadow-lg bg-cyan-600 rounded-xl hover:bg-cyan-700 shadow-cyan-600/20 active:scale-95">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add New Slide
        </a>
    </div>

    {{-- Alert Message --}}
    @if(session('success'))
        <div class="p-4 mb-6 text-sm font-bold text-green-700 bg-green-100 border border-green-200 rounded-xl animate-pulse">
            <span class="mr-2">✨</span> {{ session('success') }}
        </div>
    @endif

    {{-- Table Container --}}
    <div class="overflow-hidden bg-white border border-gray-100 shadow-xl rounded-2xl ring-1 ring-gray-900/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50">
                        <th class="px-8 py-4 text-xs font-bold tracking-wider text-gray-400 uppercase">Preview</th>
                        <th class="px-8 py-4 text-xs font-bold tracking-wider text-gray-400 uppercase">Details</th>
                        <th class="px-8 py-4 text-xs font-bold tracking-wider text-center text-gray-400 uppercase">Order</th>
                        <th class="px-8 py-4 text-xs font-bold tracking-wider text-right text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($slides as $slide)
                    <tr class="transition-colors hover:bg-gray-50/30 group">
                        <td class="px-8 py-4">
                            <div class="w-20 h-24 overflow-hidden transition-transform border border-gray-100 rounded-lg shadow-md group-hover:scale-105">
                                <img src="{{ asset('storage/' . $slide->image_path) }}" class="object-cover w-full h-full">
                            </div>
                        </td>
                        <td class="px-8 py-4">
                            <span class="inline-block px-2 py-0.5 mb-1 text-[10px] font-black tracking-widest text-orange-600 uppercase bg-orange-50 rounded">
                                {{ $slide->tag }}
                            </span>
                            <div class="text-base font-bold text-gray-900 transition-colors group-hover:text-cyan-600">{{ $slide->title }}</div>
                        </td>
                        <td class="px-8 py-4">
                           <div class="flex items-center justify-center">
                               <span class="flex items-center justify-center w-8 h-8 text-xs font-bold text-gray-600 bg-gray-100 rounded-full">
                                   {{ $slide->order }}
                               </span>
                           </div>
                        </td>
                        <td class="px-8 py-4 text-right">
                            <div class="flex items-center justify-end space-x-4">
                                <a href="{{ route('admin.hero-slides.edit', $slide) }}" 
                                   class="text-sm font-bold transition-colors text-cyan-600 hover:text-cyan-800">Edit</a>
                                
                                <form action="{{ route('admin.hero-slides.destroy', $slide) }}" method="POST" class="inline-block" 
                                      onsubmit="return confirm('Remove this slide from the carousel?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 transition-colors hover:text-red-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p class="font-medium text-gray-500">No carousel slides found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection