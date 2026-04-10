@extends('layouts.admin')

@section('title', 'Add New Author')
@section('subtitle', 'Add a new author profile to your book catalog.')

@section('admin-content')
<div class="max-w-2xl mx-auto">

    @if($errors->any())
        <div class="p-4 mb-6 text-sm text-red-700 bg-red-100 border border-red-200 rounded-xl">
            <ul class="pl-5 list-disc">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.authors.store') }}" method="POST" enctype="multipart/form-data"
          class="p-8 bg-card text-card-foreground border border-border shadow-sm rounded-3xl">
        @csrf

        <div class="space-y-6">

            <div>
                <label for="name" class="block mb-1 text-sm font-bold text-foreground">
                    Full Name <span class="text-destructive">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       placeholder="e.g. Gabriel García Márquez"
                       class="block w-full px-4 py-3 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm transition-all">
            </div>

            <div>
                <label for="bio" class="block mb-1 text-sm font-bold text-foreground">Biography</label>
                <textarea id="bio" name="bio" rows="5"
                          placeholder="A short biography of the author..."
                          class="block w-full px-4 py-3 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm transition-all">{{ old('bio') }}</textarea>
            </div>

            <div>
                <label class="block mb-1 text-sm font-bold text-foreground">Author Photo</label>

                <div x-data="{
                        imageUrl: null,
                        isDragging: false,
                        fileChosen(event) {
                            this.processFile(event.target.files[0]);
                        },
                        handleDrop(event) {
                            this.isDragging = false;
                            const file = event.dataTransfer.files[0];
                            this.processFile(file);
                            this.$refs.fileInput.files = event.dataTransfer.files;
                        },
                        processFile(file) {
                            if (!file || !file.type.match('image.*')) return;
                            const reader = new FileReader();
                            reader.readAsDataURL(file);
                            reader.onload = function(e) { this.imageUrl = e.target.result; }.bind(this);
                        }
                    }"
                    @dragover.prevent="isDragging = true"
                    @dragleave.prevent="if (!$el.contains($event.relatedTarget)) isDragging = false"
                    @drop.prevent="handleDrop($event)"
                    @click="$refs.fileInput.click()"
                    :class="isDragging ? 'border-cyan-500 bg-cyan-50' : 'border-border bg-background'"
                    class="relative flex flex-col items-center justify-center w-full p-6 overflow-hidden transition-colors border-2 border-dashed rounded-xl h-52 group hover:bg-muted cursor-pointer">

                    <input x-ref="fileInput" type="file" name="photo" @change="fileChosen" accept="image/jpeg,image/png,image/webp" class="hidden">

                    <div x-show="!imageUrl" class="text-center text-muted-foreground transition-transform pointer-events-none group-hover:scale-105">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="text-sm font-medium text-foreground"><span class="text-cyan-600">Click to upload</span> or drag and drop</p>
                        <p class="mt-1 text-xs text-muted-foreground">JPEG, PNG, or WebP. Max 2 MB.</p>
                    </div>

                    <div x-show="imageUrl" style="display: none;" @click.stop="$refs.fileInput.click()" class="absolute inset-0 z-40 flex items-center justify-center w-full h-full p-2 bg-muted cursor-pointer">
                        <img :src="imageUrl" class="object-contain w-full h-full rounded-lg shadow-sm">
                        <div class="absolute inset-0 flex items-center justify-center transition-opacity opacity-0 bg-black/40 group-hover:opacity-100 rounded-xl">
                            <span class="px-4 py-2 text-sm font-medium text-white rounded-lg bg-black/60">Click or Drop to change</span>
                        </div>
                    </div>

                </div>
                @error('photo') <span class="block mt-1 text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

        </div>

        <div class="flex items-center gap-4 pt-8 mt-8 border-t border-border">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 text-sm font-bold transition rounded-xl bg-primary text-primary-foreground hover:bg-primary/90 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Create Author
            </button>
            <a href="{{ route('admin.authors.index') }}"
               class="text-sm font-semibold text-muted-foreground transition-colors hover:text-foreground">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
