@extends('layouts.admin')

@section('title', 'Edit Author')
@section('subtitle', "Updating profile for: {$author->name}")

@section('admin-content')
<div class="max-w-2xl mx-auto">

    @if($errors->any())
        <div class="p-4 mb-6 text-sm text-red-700 bg-red-100 border border-red-200 rounded-xl">
            <ul class="pl-5 list-disc">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="p-4 mb-6 text-sm font-bold text-green-700 bg-green-100 border border-green-200 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.authors.update', $author) }}" method="POST" enctype="multipart/form-data"
          class="p-8 bg-card text-card-foreground border border-border shadow-sm rounded-3xl">
        @csrf
        @method('PUT')

        <div class="space-y-6">

            <div>
                <label for="name" class="block mb-1 text-sm font-bold text-foreground">
                    Full Name <span class="text-destructive">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $author->name) }}" required
                       placeholder="e.g. Gabriel García Márquez"
                       class="block w-full px-4 py-3 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm transition-all">
            </div>

            <div>
                <label for="bio" class="block mb-1 text-sm font-bold text-foreground">Biography</label>
                <textarea id="bio" name="bio" rows="5"
                          placeholder="A short biography of the author..."
                          class="block w-full px-4 py-3 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm transition-all">{{ old('bio', $author->bio) }}</textarea>
            </div>

            <div>
                <label for="photo" class="block mb-1 text-sm font-bold text-foreground">Author Photo</label>
                @if($author->photo_path)
                    <div class="flex items-center gap-3 mb-3">
                        <img src="{{ asset('storage/' . $author->photo_path) }}"
                             alt="{{ $author->name }}"
                             class="object-cover w-16 h-16 rounded-full border border-border">
                        <p class="text-xs text-muted-foreground">Current photo — upload a new file to replace it.</p>
                    </div>
                @endif
                <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp"
                       class="block w-full text-sm text-muted-foreground border border-input rounded-[var(--radius)] bg-background cursor-pointer
                              file:mr-4 file:py-2 file:px-4 file:border-0 file:rounded-lg
                              file:text-sm file:font-bold file:bg-muted file:text-foreground
                              hover:file:bg-muted/80">
                <p class="mt-1 text-xs text-muted-foreground">JPEG, PNG, or WebP. Max 2 MB. Leave blank to keep the current photo.</p>
            </div>

        </div>

        <div class="flex items-center gap-4 pt-8 mt-8 border-t border-border">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 text-sm font-bold transition rounded-xl bg-primary text-primary-foreground hover:bg-primary/90 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Save Changes
            </button>
            <a href="{{ route('admin.authors.index') }}"
               class="text-sm font-semibold text-muted-foreground transition-colors hover:text-foreground">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
