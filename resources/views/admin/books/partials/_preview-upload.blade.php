{{-- ============================================================
     Peek Inside Media — upload new previews + manage existing
     ============================================================ --}}

{{-- Upload Form --}}
<form action="{{ route('admin.product-previews.store', $book) }}"
      method="POST"
      enctype="multipart/form-data"
      class="p-5 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50/50 hover:border-cyan-300 transition-colors">
    @csrf

    <p class="mb-3 text-xs font-bold tracking-wider text-gray-400 uppercase">Add New Preview</p>

    <div class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-48">
            <label class="block mb-1 text-xs font-semibold text-gray-500">File <span class="text-gray-400 font-normal">(image or video)</span></label>
            <input type="file" name="file" accept="image/*,video/*" required
                   class="block w-full text-sm text-gray-500 cursor-pointer file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100 transition-all">
        </div>
        <div class="w-24">
            <label class="block mb-1 text-xs font-semibold text-gray-500">Sort Order</label>
            <input type="number" name="sort_order" min="0" max="255" value="0"
                   class="block w-full py-2 text-sm border-gray-200 rounded-lg shadow-sm focus:ring-cyan-500 focus:border-cyan-500">
        </div>
        <button type="submit"
                class="px-5 py-2 text-sm font-bold text-white bg-cyan-600 rounded-lg hover:bg-cyan-700 active:scale-95 transition shrink-0">
            Upload
        </button>
    </div>
</form>

{{-- Existing Previews Grid --}}
@php $previews = $book->previews; @endphp

@if($previews->isNotEmpty())
<div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
    @foreach($previews as $preview)
    <div class="relative group overflow-hidden rounded-xl bg-gray-100 ring-1 ring-gray-900/5 shadow-sm">

        {{-- Thumbnail --}}
        @if($preview->type === 'image')
            <img src="{{ asset('storage/' . $preview->path) }}"
                 alt="Preview {{ $loop->iteration }}"
                 class="object-cover w-full h-28">
        @else
            <div class="flex flex-col items-center justify-center w-full h-28 text-gray-400">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <span class="mt-1 text-xs font-semibold">Video</span>
            </div>
        @endif

        {{-- Sort order badge --}}
        <span class="absolute top-1.5 left-1.5 px-1.5 py-0.5 text-[10px] font-bold text-white bg-black/50 rounded-md backdrop-blur-sm">
            #{{ $preview->sort_order }}
        </span>

        {{-- Delete button (always visible on mobile, hover on desktop) --}}
        <div class="absolute inset-x-0 bottom-0 flex justify-center p-2 bg-gradient-to-t from-black/60 to-transparent
                    opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
            <form action="{{ route('admin.product-previews.destroy', $preview) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit"
                        onclick="return confirm('Delete this preview?')"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@else
    <p class="mt-4 text-sm text-center text-gray-400 py-6">No preview media uploaded yet.</p>
@endif
