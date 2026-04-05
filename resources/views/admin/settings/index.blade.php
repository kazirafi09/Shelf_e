@extends('layouts.admin')

@section('title', 'Store Settings')
@section('subtitle', 'Manage the announcement banner, FOMO countdown, and delivery prices.')

@section('admin-content')

    @if(session('success'))
        <div x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 3000)"
             x-show="show"
             x-transition
             class="flex items-center gap-3 px-5 py-3 mb-6 text-sm font-semibold text-green-800 border border-green-200 rounded-xl bg-green-50">
            <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        <div class="space-y-6">

            {{-- Announcement Banner --}}
            <div class="p-6 bg-card text-card-foreground border border-border rounded-2xl shadow-sm">
                <h2 class="mb-1 text-base font-bold tracking-tight text-foreground">Announcement Banner</h2>
                <p class="mb-5 text-sm text-muted-foreground">This text appears in the top bar on every public page.</p>

                <label for="announcement_text" class="block mb-2 text-sm font-semibold text-foreground">
                    Banner Text
                </label>
                <input
                    type="text"
                    id="announcement_text"
                    name="announcement_text"
                    value="{{ old('announcement_text', $announcementText) }}"
                    maxlength="200"
                    class="w-full px-4 py-2.5 text-sm border border-border rounded-xl bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-gray-400 transition"
                    placeholder="e.g. Free Standard Shipping on orders over ৳1000!"
                >
                @error('announcement_text')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- FOMO Countdown --}}
            <div class="p-6 bg-card text-card-foreground border border-border rounded-2xl shadow-sm">
                <h2 class="mb-1 text-base font-bold tracking-tight text-foreground">FOMO Deal Countdown</h2>
                <p class="mb-5 text-sm text-muted-foreground">
                    Set when the current deal expires. The top bar will count down to this moment in real time.
                    Pick a future date &amp; time to restart the urgency.
                </p>

                <label for="fomo_ends_at" class="block mb-2 text-sm font-semibold text-foreground">
                    Deal Ends At
                </label>
                <input
                    type="datetime-local"
                    id="fomo_ends_at"
                    name="fomo_ends_at"
                    value="{{ old('fomo_ends_at', \Carbon\Carbon::parse($fomoEndsAt)->format('Y-m-d\TH:i')) }}"
                    class="px-4 py-2.5 text-sm border border-border rounded-xl bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-gray-400 transition"
                >
                @error('fomo_ends_at')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror

                {{-- Live preview --}}
                <div class="flex items-center gap-2 mt-5 text-xs text-muted-foreground">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Visitors will see a countdown like:
                    <span class="px-2 py-0.5 font-mono font-bold text-white bg-gray-900 rounded">Deal ends in: 04:32:18</span>
                </div>
            </div>

            {{-- Delivery Prices --}}
            <div class="p-6 bg-card text-card-foreground border border-border rounded-2xl shadow-sm">
                <h2 class="mb-1 text-base font-bold tracking-tight text-foreground">Delivery Prices</h2>
                <p class="mb-5 text-sm text-muted-foreground">Set the shipping cost charged at checkout for each delivery method.</p>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="shipping_inside_dhaka" class="block mb-2 text-sm font-semibold text-foreground">
                            Inside Dhaka (৳)
                        </label>
                        <input
                            type="number"
                            id="shipping_inside_dhaka"
                            name="shipping_inside_dhaka"
                            value="{{ old('shipping_inside_dhaka', $shippingInsideDhaka) }}"
                            min="0"
                            class="w-full px-4 py-2.5 text-sm border border-border rounded-xl bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-gray-400 transition"
                        >
                        @error('shipping_inside_dhaka')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-muted-foreground">Applied when the customer's division is Dhaka.</p>
                    </div>

                    <div>
                        <label for="shipping_outside_dhaka" class="block mb-2 text-sm font-semibold text-foreground">
                            Outside Dhaka (৳)
                        </label>
                        <input
                            type="number"
                            id="shipping_outside_dhaka"
                            name="shipping_outside_dhaka"
                            value="{{ old('shipping_outside_dhaka', $shippingOutsideDhaka) }}"
                            min="0"
                            class="w-full px-4 py-2.5 text-sm border border-border rounded-xl bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-gray-400 transition"
                        >
                        @error('shipping_outside_dhaka')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-muted-foreground">Applied for all other divisions (Chattogram, Khulna, Rajshahi, etc.).</p>
                    </div>
                </div>
            </div>

            {{-- Returns Policy --}}
            <div class="p-6 bg-card text-card-foreground border border-border rounded-2xl shadow-sm">
                <h2 class="mb-1 text-base font-bold tracking-tight text-foreground">Returns Policy</h2>
                <p class="mb-5 text-sm text-muted-foreground">This content is shown on the public Returns Policy page.</p>

                <label for="returns_policy" class="block mb-2 text-sm font-semibold text-foreground">
                    Policy Content
                </label>
                <textarea
                    id="returns_policy"
                    name="returns_policy"
                    rows="8"
                    maxlength="5000"
                    class="w-full px-4 py-2.5 text-sm border border-border rounded-xl bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-gray-400 transition resize-y"
                    placeholder="Describe your returns and refund policy…"
                >{{ old('returns_policy', $returnsPolicy) }}</textarea>
                @error('returns_policy')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- FAQ --}}
            <div class="p-6 bg-card text-card-foreground border border-border rounded-2xl shadow-sm">
                <h2 class="mb-1 text-base font-bold tracking-tight text-foreground">FAQ</h2>
                <p class="mb-5 text-sm text-muted-foreground">This content is shown on the public FAQ page. Use plain text or simple formatting.</p>

                <label for="faq_content" class="block mb-2 text-sm font-semibold text-foreground">
                    FAQ Content
                </label>
                <textarea
                    id="faq_content"
                    name="faq_content"
                    rows="12"
                    maxlength="10000"
                    class="w-full px-4 py-2.5 text-sm border border-border rounded-xl bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-gray-400 transition resize-y"
                    placeholder="List your frequently asked questions and answers…"
                >{{ old('faq_content', $faqContent) }}</textarea>
                @error('faq_content')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="px-8 py-2.5 text-sm font-bold text-white transition bg-gray-900 rounded-xl hover:bg-gray-800 hover:-translate-y-0.5 shadow-sm">
                    Save Settings
                </button>
            </div>

        </div>
    </form>

@endsection
