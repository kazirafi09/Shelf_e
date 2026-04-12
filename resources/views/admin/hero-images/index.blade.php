@extends('layouts.admin')

@section('title', 'Hero Images')
@section('subtitle', 'Current images displayed in the bento-grid hero section on the homepage.')

@section('admin-content')

    {{-- Info banner --}}
    <div class="flex items-start gap-3 p-4 mb-8 text-sm text-blue-800 border border-blue-200 rounded-xl bg-blue-50">
        <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p>These are the five bento-grid images shown on the homepage hero. Images marked <strong>Custom</strong> are served from storage; <strong>Default</strong> images are the original static files.</p>
    </div>

    {{-- Image grid --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($slots as $slot => $info)
            <div class="flex flex-col overflow-hidden bg-card text-card-foreground border border-border rounded-2xl shadow-sm">

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
                        src="{{ $info['previewUrl'] }}?v={{ time() }}"
                        alt="Hero image {{ $slot }}"
                        class="object-cover w-full h-full"
                        loading="lazy"
                    >
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
            <div class="rounded-lg bg-rose-100 border border-rose-300 flex items-center justify-center font-black text-rose-700 text-sm py-3" style="grid-column:2; grid-row:3;">5</div>
        </div>
        <p class="mt-3 text-xs text-muted-foreground">Images 1–3 are visible on mobile. Images 4–5 are desktop-only.</p>
    </div>

@endsection
