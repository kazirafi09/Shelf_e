@extends('layouts.admin')

@section('title', 'Hero Images')
@section('subtitle', 'Upload or revert the five bento-grid images shown on the homepage hero section.')

@section('admin-content')

    {{-- Flash messages --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="flex items-center gap-3 p-4 mb-6 text-sm font-medium text-emerald-800 border border-emerald-200 rounded-xl bg-emerald-50">
            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Info banner --}}
    <div class="flex items-start gap-3 p-4 mb-8 text-sm text-blue-800 border border-blue-200 rounded-xl bg-blue-50">
        <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p>Upload a JPG, PNG, or WebP image (max 10 MB) to replace any slot. The image is automatically resized to three responsive sizes. <strong>Custom</strong> images are served from storage; <strong>Default</strong> images are the original static files.</p>
    </div>

    {{-- Image grid --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($slots as $slot => $info)
            <div
                class="flex flex-col overflow-hidden bg-card text-card-foreground border border-border rounded-2xl shadow-sm"
                x-data="{ preview: '{{ $info['previewUrl'] }}', uploading: false }"
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
                    <img
                        :src="preview"
                        alt="Hero image {{ $slot }}"
                        class="object-cover w-full h-full"
                        loading="lazy"
                    >
                    {{-- Upload overlay spinner --}}
                    <div x-show="uploading" class="absolute inset-0 flex items-center justify-center bg-white/70 backdrop-blur-sm">
                        <svg class="w-8 h-8 animate-spin text-gray-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                    </div>
                </div>

                {{-- Upload form --}}
                <form
                    method="POST"
                    action="{{ route('hero-images.store', $slot) }}"
                    enctype="multipart/form-data"
                    class="flex flex-col gap-3 p-4 border-t border-border"
                    x-on:submit="uploading = true"
                >
                    @csrf

                    <label class="flex flex-col gap-1">
                        <span class="text-xs font-semibold text-muted-foreground">Replace image</span>
                        <input
                            type="file"
                            name="image"
                            accept="image/jpeg,image/png,image/webp"
                            required
                            class="block w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer"
                            x-on:change="
                                const f = $event.target.files[0];
                                if (f) preview = URL.createObjectURL(f);
                            "
                        >
                    </label>

                    <button
                        type="submit"
                        class="w-full py-2 text-sm font-semibold text-white bg-gray-900 rounded-lg hover:bg-gray-700 active:scale-95 transition-all duration-150"
                    >
                        Upload
                    </button>
                </form>

                {{-- Revert button (only when custom image exists) --}}
                @if($info['hasCustom'])
                    <form
                        method="POST"
                        action="{{ route('hero-images.destroy', $slot) }}"
                        class="px-4 pb-4"
                        x-on:submit.prevent="
                            if (confirm('Revert image {{ $slot }} to the default?')) {
                                uploading = true;
                                $el.submit();
                            }
                        "
                    >
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="w-full py-2 text-sm font-semibold text-rose-700 bg-rose-50 border border-rose-200 rounded-lg hover:bg-rose-100 active:scale-95 transition-all duration-150"
                        >
                            Revert to default
                        </button>
                    </form>
                @endif

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
            <div class="rounded-lg bg-rose-100 border border-rose-300 flex items-center justify-center font-black text-rose-700 text-sm py-3" style="grid-column:2; grid-row:3;">5</div>
        </div>
        <p class="mt-3 text-xs text-muted-foreground">Images 1–3 are visible on mobile. Images 4–5 are desktop-only.</p>
    </div>

@endsection
