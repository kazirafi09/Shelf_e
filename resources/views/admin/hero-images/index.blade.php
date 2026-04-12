@extends('layouts.admin')

@section('title', 'Hero Images')
@section('subtitle', 'Manage the 5 bento-box images displayed on the homepage hero section.')

@section('admin-content')

    {{-- Flash messages --}}
    @if(session('success'))
        <div x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 3500)"
             x-show="show"
             x-transition
             class="flex items-center gap-3 px-5 py-3 mb-6 text-sm font-semibold text-green-800 border border-green-200 rounded-xl bg-green-50">
            <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 mb-6 text-sm text-red-700 bg-red-100 border border-red-200 rounded-xl">
            <ul class="pl-5 list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Info banner --}}
    <div class="flex items-start gap-3 p-4 mb-8 text-sm text-blue-800 border border-blue-200 rounded-xl bg-blue-50">
        <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p>Uploaded images are automatically resized and converted to <strong>WebP</strong> for optimal performance.
        Recommended size: <strong>1440 × 960 px</strong> or larger. Max file size: 10 MB. Supported formats: JPG, PNG, WebP.</p>
    </div>

    {{-- Image grid --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">

        @foreach($slots as $slot => $info)
            <div
                x-data="{ uploading: false }"
                class="flex flex-col overflow-hidden bg-card text-card-foreground border border-border rounded-2xl shadow-sm"
            >
                {{-- Slot header --}}
                <div class="flex items-center justify-between px-5 py-3 border-b border-border">
                    <span class="text-sm font-bold text-foreground">Image {{ $slot }}</span>
                    @if($info['hasCustom'])
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Custom
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-bold uppercase tracking-wider text-gray-500 bg-gray-50 border border-gray-200 rounded-full">
                            Default
                        </span>
                    @endif
                </div>

                {{-- Preview --}}
                <div class="relative bg-gray-100 aspect-video">
                    @if($info['previewUrl'])
                        <img
                            src="{{ $info['previewUrl'] }}"
                            alt="Hero image {{ $slot }}"
                            class="object-cover w-full h-full"
                            loading="lazy"
                        >
                    @else
                        <div class="flex items-center justify-center w-full h-full text-gray-400">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 p-4">

                    {{-- Upload form --}}
                    <form
                        action="{{ route('admin.hero-images.update', $slot) }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="flex-1"
                        x-on:submit="uploading = true"
                    >
                        @csrf
                        @method('PUT')
                        <label class="flex items-center justify-center w-full gap-2 px-4 py-2 text-xs font-bold tracking-wide transition-colors border rounded-xl cursor-pointer border-border text-foreground hover:bg-accent"
                               x-bind:class="uploading ? 'opacity-60 pointer-events-none' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            <span x-text="uploading ? 'Uploading…' : 'Upload New'"></span>
                            <input
                                type="file"
                                name="image"
                                accept="image/jpeg,image/png,image/webp"
                                class="sr-only"
                                x-on:change="$el.closest('form').requestSubmit()"
                            >
                        </label>
                    </form>

                    {{-- Delete / reset button (only show if custom image exists) --}}
                    @if($info['hasCustom'])
                        <form
                            action="{{ route('admin.hero-images.destroy', $slot) }}"
                            method="POST"
                            x-on:submit.prevent="if(confirm('Reset image {{ $slot }} to default?')) $el.submit()"
                        >
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                title="Reset to default"
                                class="inline-flex items-center justify-center w-10 h-10 text-red-600 transition-colors border border-red-200 rounded-xl hover:bg-red-50"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    @endif

                </div>
            </div>
        @endforeach

    </div>

    {{-- Layout reference --}}
    <div class="p-5 mt-8 border border-dashed rounded-2xl border-border bg-muted/30">
        <p class="mb-3 text-xs font-bold tracking-wider text-muted-foreground uppercase">Bento Layout Reference</p>
        <div class="grid gap-1.5 max-w-xs" style="grid-template-columns: 2fr 1fr; grid-template-rows: auto auto auto;">
            <div class="rounded-lg bg-amber-100 border border-amber-300 flex items-center justify-center font-black text-amber-700 text-sm py-6" style="grid-column:1; grid-row:1/3;">1<br><span class="text-xs font-normal">Large</span></div>
            <div class="rounded-lg bg-blue-100 border border-blue-300 flex items-center justify-center font-black text-blue-700 text-sm py-3" style="grid-column:2; grid-row:1;">2</div>
            <div class="rounded-lg bg-purple-100 border border-purple-300 flex items-center justify-center font-black text-purple-700 text-sm py-3" style="grid-column:2; grid-row:2;">3</div>
            <div class="rounded-lg bg-green-100 border border-green-300 flex items-center justify-center font-black text-green-700 text-sm py-3" style="grid-column:1; grid-row:3;">4</div>
            <div class="rounded-lg bg-rose-100 border border-rose-300 flex items-center justify-center font-black text-rose-700 text-sm py-3" style="grid-column:2; grid-row:3;">5<br><span class="text-xs font-normal">Wide</span></div>
        </div>
        <p class="mt-3 text-xs text-muted-foreground">Images 1–3 are visible on mobile. Images 4–5 are desktop-only.</p>
    </div>

@endsection
