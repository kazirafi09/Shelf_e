@props(['src', 'zoomSrc' => null, 'alt' => ''])

@php
    $hasSrc    = !empty($src);
    $zoomImage = !empty($zoomSrc) ? $zoomSrc : $src;
@endphp

{{--
    Image Magnifier Component
    ──────────────────────────────────────────────────────────────────
    Props
      src      – standard-resolution URL rendered in the main display box
      zoom-src – high-resolution URL used exclusively by the zoom pane
                 (falls back to src when omitted)
      alt      – img alt text

    Behaviour
      • Hover reveals a tracking lens overlay (sharp rectangle, white/gray theme)
        that follows the cursor and shows exactly which region is magnified
      • A fixed 500 × 600 px zoom pane appears to the right, rendering the
        hi-res image panned to match the lens position
      • Both elements are perfectly synced via pixel-accurate math:
            bgX = (lensLeft / W) × (zf / (zf − 1)) × 100
            bgY = (lensTop  / H) × (zf / (zf − 1)) × 100
        where zf = background-size-percent / 100
      • Zoom panel + lens hidden on screens < lg (no room beside the image)
      • No JS runs when src is empty
    ──────────────────────────────────────────────────────────────────
--}}
<div
    class="relative"
    x-data="{
        active:   false,
        bgX:      50,
        bgY:      50,
        lensLeft: 0,
        lensTop:  0,
        lensW:    0,
        lensH:    0,

        /* zoom factor — must match background-size: (zf * 100)% below */
        zf: 2.8,

        track(e) {
            const r  = this.$refs.imgBox.getBoundingClientRect();
            const W  = r.width,      H  = r.height;
            const lw = W / this.zf,  lh = H / this.zf;

            /* cursor position relative to the image box */
            const cx = e.clientX - r.left;
            const cy = e.clientY - r.top;

            /* clamp lens so it never overflows the image edges */
            const left = Math.min(Math.max(cx - lw / 2, 0), W - lw);
            const top  = Math.min(Math.max(cy - lh / 2, 0), H - lh);

            this.lensLeft = left;
            this.lensTop  = top;
            this.lensW    = lw;
            this.lensH    = lh;

            /*
             * Pixel-accurate sync with CSS background-position percentage.
             *
             * background-position X% aligns the X%-point of the bg image with
             * the X%-point of the container — it is NOT a simple left-offset.
             * Derivation:
             *   visible_left_px = (bgWidth - containerWidth) × (X / 100)
             *   bgWidth         = containerWidth × zf
             *   we want: visible_left_px / bgWidth = lensLeft / W
             *   → X = (lensLeft / W) × (zf / (zf − 1)) × 100
             */
            const k  = this.zf / (this.zf - 1);
            this.bgX = (left / W) * k * 100;
            this.bgY = (top  / H) * k * 100;
        }
    }"
>

    {{-- ── Main image box ── --}}
    <div
        x-ref="imgBox"
        @mouseenter="active = true"
        @mouseleave="active = false; bgX = 50; bgY = 50"
        @mousemove="track($event)"
        class="relative w-full aspect-[2/3] bg-muted rounded-2xl overflow-hidden border border-border shadow-xl select-none {{ $hasSrc ? 'lg:cursor-crosshair' : '' }}"
    >
        @if($hasSrc)
            <img
                src="{{ $src }}"
                alt="{{ $alt }}"
                class="object-cover w-full h-full pointer-events-none"
            >

            {{-- Tracking lens overlay
                 Sharp rectangle (no rounding), white/gray theme.
                 Sized and positioned by Alpine to represent the exact region
                 that the zoom pane is currently displaying.
            --}}
            <div
                x-show="active"
                x-cloak
                :style="`left: ${lensLeft}px; top: ${lensTop}px; width: ${lensW}px; height: ${lensH}px;`"
                class="hidden lg:block absolute bg-white/40 border border-gray-300 pointer-events-none z-10"
            ></div>

            {{-- "Hover to zoom" hint — fades out once hovering begins --}}
            <div
                class="absolute bottom-3 right-3 hidden lg:flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium text-white bg-black/50 rounded-lg pointer-events-none transition-opacity duration-200"
                :class="active ? 'opacity-0' : 'opacity-100'"
            >
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Hover to zoom
            </div>
        @else
            <div class="flex items-center justify-center w-full h-full text-sm text-muted-foreground">
                Cover Image
            </div>
        @endif
    </div>

    {{-- ── Zoom pane ──
         Uses zoom-src (hi-res) for a sharp background.
         Fixed at 500 × 600 px — book-cover proportions (5:6).
         Sharp edges: no rounded corners.
         Positioned left: calc(100% + 2.5rem) to land at the next grid column.
         z-20 sits above the sticky purchase widget.
         Hidden below lg.
    --}}
    @if($hasSrc)
        <div
            x-show="active"
            x-cloak
            :style="`
                background-image:    url('{{ $zoomImage }}');
                background-size:     280%;
                background-position: ${bgX}% ${bgY}%;
                background-repeat:   no-repeat;
            `"
            class="hidden lg:block absolute top-0 left-[calc(100%+2.5rem)] w-[500px] h-[600px] border border-border shadow-2xl bg-muted z-20"
        ></div>
    @endif

</div>
