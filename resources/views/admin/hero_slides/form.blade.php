@extends('layouts.admin')

{{-- 1. Set the Admin Header Titles --}}
@section('title', isset($heroSlide) ? 'Edit Slide Details' : 'Upload New Slide')
@section('subtitle', 'Configure the images and text for your homepage 3D carousel.')

@section('admin-content')
<div class="max-w-3xl mx-auto">
    
    {{-- Breadcrumb for easy navigation --}}
    <div class="mb-6 text-sm">
        <a href="{{ route('admin.hero-slides.index') }}" class="font-medium text-cyan-600 hover:text-cyan-700">&larr; Back to Slide List</a>
    </div>

    <div class="p-8 bg-white border border-gray-100 shadow-xl rounded-3xl">
        <form action="{{ isset($heroSlide) ? route('admin.hero-slides.update', $heroSlide) : route('admin.hero-slides.store') }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="space-y-8">
            @csrf
            @if(isset($heroSlide)) @method('PUT') @endif

            {{-- Image Upload Section --}}
            <div class="p-6 border-2 border-gray-200 border-dashed rounded-2xl bg-gray-50/50">
                <label class="block mb-4 text-sm font-bold tracking-wide text-gray-700 uppercase">Slide Cover Image</label>
                
                @if(isset($heroSlide))
                    <div class="mb-4">
                        <p class="mb-2 text-xs font-semibold text-gray-400">Current Image:</p>
                        <div class="relative w-32 h-44 group">
                             <img src="{{ asset('storage/' . $heroSlide->image_path) }}" 
                                 class="object-cover w-full h-full transition-transform border-2 border-white shadow-lg rounded-xl group-hover:scale-105">
                        </div>
                    </div>
                @endif

                <input type="file" name="image" 
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100 cursor-pointer" 
                       {{ isset($heroSlide) ? '' : 'required' }}>
                
                <p class="mt-3 text-xs text-gray-400">Recommended size: 800x1000px. Supports JPG, PNG, WebP.</p>
                @error('image') <p class="mt-2 text-sm font-bold text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                {{-- Tag Input --}}
                <div>
                    <label class="block mb-2 text-sm font-bold tracking-wide text-gray-700 uppercase">Tag (e.g. #01)</label>
                    <input type="text" name="tag" value="{{ old('tag', $heroSlide->tag ?? '') }}" 
                           placeholder="#01" 
                           class="w-full px-5 py-3 transition-all border border-gray-200 outline-none rounded-xl focus:ring-4 focus:ring-cyan-500/10 focus:border-cyan-500 bg-gray-50/50">
                </div>

                {{-- Sort Order Input --}}
                <div>
                    <label class="block mb-2 text-sm font-bold tracking-wide text-gray-700 uppercase">Sort Order</label>
                    <input type="number" name="order" value="{{ old('order', $heroSlide->order ?? 0) }}" 
                           class="w-full px-5 py-3 transition-all border border-gray-200 outline-none rounded-xl focus:ring-4 focus:ring-cyan-500/10 focus:border-cyan-500 bg-gray-50/50">
                </div>
            </div>

            {{-- Title Input --}}
            <div>
                <label class="block mb-2 text-sm font-bold tracking-wide text-gray-700 uppercase">Slide Title</label>
                <input type="text" name="title" value="{{ old('title', $heroSlide->title ?? '') }}" 
                       placeholder="Enter a catchy title..." 
                       class="w-full px-5 py-3 transition-all border border-gray-200 outline-none rounded-xl focus:ring-4 focus:ring-cyan-500/10 focus:border-cyan-500 bg-gray-50/50">
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col-reverse gap-4 pt-6 border-t border-gray-100 md:flex-row">
                <a href="{{ route('admin.hero-slides.index') }}" 
                   class="flex items-center justify-center px-8 py-4 text-sm font-bold text-gray-500 transition-colors bg-gray-100 rounded-xl hover:bg-gray-200">
                    Discard Changes
                </a>
                <button type="submit" 
                        class="flex-1 py-4 text-base font-bold text-white transition-all shadow-lg bg-cyan-600 rounded-xl hover:bg-cyan-700 shadow-cyan-600/20 active:scale-95">
                    {{ isset($heroSlide) ? 'Update Slide' : 'Publish Slide' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection