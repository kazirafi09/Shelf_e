@extends('layouts.admin')

@section('title', 'Store Settings')
@section('subtitle', 'Manage the announcement banner and delivery prices.')

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

            {{-- Bkash Payment --}}
            <div class="p-6 bg-card text-card-foreground border border-border rounded-2xl shadow-sm">
                <h2 class="mb-1 text-base font-bold tracking-tight text-foreground">Bkash Payment</h2>
                <p class="mb-5 text-sm text-muted-foreground">The Bkash merchant number customers will send payment to at checkout. Leave blank to hide the Bkash payment option.</p>

                <label for="bkash_number" class="block mb-2 text-sm font-semibold text-foreground">
                    Bkash Number
                </label>
                <input
                    type="text"
                    id="bkash_number"
                    name="bkash_number"
                    value="{{ old('bkash_number', $bkashNumber) }}"
                    maxlength="20"
                    class="w-full max-w-xs px-4 py-2.5 text-sm border border-border rounded-xl bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-gray-400 transition"
                    placeholder="e.g. 01XXXXXXXXX"
                >
                @error('bkash_number')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1.5 text-xs text-muted-foreground">This number will be displayed to the customer when they select Bkash at checkout.</p>
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

            {{-- Newsletter Discount --}}
            <div class="p-6 bg-card text-card-foreground border border-border rounded-2xl shadow-sm">
                <h2 class="mb-1 text-base font-bold tracking-tight text-foreground">Newsletter Discount (FIRST15)</h2>
                <p class="mb-5 text-sm text-muted-foreground">Configure the welcome discount given to new newsletter subscribers. The cap limits the maximum taka value of the discount (0 = no cap).</p>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="newsletter_discount_percent" class="block mb-2 text-sm font-semibold text-foreground">
                            Discount Percentage (%)
                        </label>
                        <input
                            type="number"
                            id="newsletter_discount_percent"
                            name="newsletter_discount_percent"
                            value="{{ old('newsletter_discount_percent', $newsletterDiscountPercent) }}"
                            min="1"
                            max="100"
                            class="w-full px-4 py-2.5 text-sm border border-border rounded-xl bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-gray-400 transition"
                        >
                        @error('newsletter_discount_percent')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-muted-foreground">Percentage off the order subtotal (1–100).</p>
                    </div>

                    <div>
                        <label for="newsletter_discount_cap" class="block mb-2 text-sm font-semibold text-foreground">
                            Maximum Discount (৳)
                        </label>
                        <input
                            type="number"
                            id="newsletter_discount_cap"
                            name="newsletter_discount_cap"
                            value="{{ old('newsletter_discount_cap', $newsletterDiscountCap) }}"
                            min="0"
                            class="w-full px-4 py-2.5 text-sm border border-border rounded-xl bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-gray-400 transition"
                        >
                        @error('newsletter_discount_cap')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-muted-foreground">The discount will not exceed this amount in taka. Set to 0 for no cap.</p>
                    </div>
                </div>
            </div>

            {{-- About Us --}}
            <div class="p-6 bg-card text-card-foreground border border-border rounded-2xl shadow-sm"
                 x-data="{ aboutUs: {{ Js::from(old('about_us', $aboutUs)) }}, maxLen: 2000 }">
                <h2 class="mb-1 text-base font-bold tracking-tight text-foreground">About Us</h2>
                <p class="mb-5 text-sm text-muted-foreground">This text appears in the footer's "About Us" section on every public page. Long text will show a "Read more" toggle in the footer.</p>

                <div class="flex items-center justify-between mb-2">
                    <label for="about_us" class="text-sm font-semibold text-foreground">
                        About Us Text
                    </label>
                    <span class="text-xs tabular-nums"
                          :class="aboutUs.length > maxLen ? 'text-red-600 font-semibold' : 'text-muted-foreground'">
                        <span x-text="aboutUs.length"></span> / <span x-text="maxLen"></span>
                    </span>
                </div>
                <textarea
                    id="about_us"
                    name="about_us"
                    rows="10"
                    maxlength="2000"
                    x-model="aboutUs"
                    class="w-full px-4 py-2.5 text-sm border border-border rounded-xl bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-gray-400 transition resize-y"
                    placeholder="Write a description about your store…"
                ></textarea>
                @error('about_us')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1.5 text-xs text-muted-foreground">Maximum 2,000 characters. Separate paragraphs with a blank line.</p>
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
