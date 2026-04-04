@if($reviews->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 bg-card text-card-foreground border border-border rounded-2xl shadow-sm ring-1 ring-gray-900/5">
        <svg class="w-12 h-12 mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <p class="text-sm font-medium text-muted-foreground">No {{ $status }} reviews.</p>
    </div>
@else
    <div class="space-y-4">
        @foreach($reviews as $review)
        <div class="bg-card text-card-foreground border border-border rounded-2xl shadow-sm ring-1 ring-gray-900/5 p-6">
            <div class="flex items-start justify-between gap-4">

                {{-- Left: review meta --}}
                <div class="flex-1 min-w-0">
                    {{-- Product + user --}}
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="text-sm font-bold text-foreground truncate">
                            {{ $review->product?->title ?? '(deleted product)' }}
                        </span>
                        <span class="text-gray-300">·</span>
                        <span class="text-xs text-muted-foreground">
                            by {{ $review->user?->name ?? 'Deleted User' }}
                        </span>
                        @if($review->is_verified_purchase)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Verified Purchase
                            </span>
                        @endif
                    </div>

                    {{-- Star rating --}}
                    <div class="flex items-center gap-0.5 mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $review->rating)
                                <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @else
                                <svg class="w-4 h-4 text-gray-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endif
                        @endfor
                        <span class="ml-1.5 text-xs text-muted-foreground">{{ $review->created_at->format('M d, Y') }}</span>
                    </div>

                    {{-- Title + body --}}
                    @if($review->title)
                        <p class="mb-1 text-sm font-semibold text-foreground">{{ $review->title }}</p>
                    @endif
                    <p class="text-sm leading-relaxed text-muted-foreground line-clamp-3">{{ $review->body }}</p>
                </div>

                {{-- Right: action buttons --}}
                <div class="flex flex-col gap-2 shrink-0">
                    @if($status !== 'approved')
                        <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Approve
                            </button>
                        </form>
                    @endif

                    @if($status !== 'rejected')
                        <form action="{{ route('admin.reviews.reject', $review) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Reject
                            </button>
                        </form>
                    @endif
                </div>

            </div>
        </div>
        @endforeach
    </div>
@endif
