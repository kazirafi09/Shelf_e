@extends('layouts.app')

@section('content')
<div class="container px-4 py-8 mx-auto" x-data="{
    division: '{{ $prefillDivision ?? 'Dhaka' }}',
    shippingInsideDhaka: {{ $insideDhakaRate }},
    shippingOutsideDhaka: {{ $outsideDhakaRate }},
    subtotal: {{ $subtotal }},
    couponCode: '{{ old('coupon_code', '') }}',
    couponDiscount: 0,
    couponValid: null,
    couponMessage: '',
    couponChecking: false,
    activeReward: @json($activeReward ?? null),
    applyReward: false,
    get shipping() {
        if (this.division === 'Dhaka' && this.subtotal >= 1500) return 0;
        return this.division === 'Dhaka' ? this.shippingInsideDhaka : this.shippingOutsideDhaka;
    },
    get rewardDiscount() {
        if (!this.applyReward || !this.activeReward) return 0;
        return Math.min(this.activeReward.shipping_discount, this.shipping);
    },
    get grandTotal() {
        return this.subtotal + this.shipping - this.couponDiscount - this.rewardDiscount;
    },
    async applyVoucher() {
        const code = this.couponCode.trim().toUpperCase();
        if (!code) { this.couponDiscount = 0; this.couponValid = null; this.couponMessage = ''; return; }
        this.couponChecking = true;
        this.couponValid = null;
        this.couponMessage = '';
        try {
            const res = await fetch(`/api/voucher/validate?code=${encodeURIComponent(code)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.valid) {
                if (data.discount_type === 'percentage') {
                    this.couponDiscount = Math.round(this.subtotal * (data.discount_value / 100));
                } else {
                    this.couponDiscount = Math.min(data.discount_value, this.subtotal);
                }
                this.couponValid = true;
                this.couponMessage = data.description || 'Discount applied!';
            } else {
                this.couponDiscount = 0;
                this.couponValid = false;
                this.couponMessage = data.message || 'Invalid code.';
            }
        } catch(e) {
            this.couponDiscount = 0;
            this.couponValid = false;
            this.couponMessage = 'Could not verify code. Try again.';
        }
        this.couponChecking = false;
    },
    fillAddress(addr) {
        this.$refs.fieldName.value      = addr.name;
        this.$refs.fieldEmail.value     = addr.email;
        this.$refs.fieldAddress.value   = addr.address;
        this.division                   = addr.division;
        this.$refs.fieldDistrict.value  = addr.district;
        this.$refs.fieldPostal.value    = addr.postal_code ?? '';
        this.$refs.fieldPhone.value     = addr.phone;
        this.$refs.fieldPhone.dispatchEvent(new Event('input'));
    }
}">
    
    <div class="mb-8 text-sm text-muted-foreground">
        <a href="/" class="hover:text-gray-700">Home</a> <span class="mx-2">></span>
        <a href="/categories" class="hover:text-gray-700">Categories</a> <span class="mx-2">></span>
        <span class="text-foreground">Checkout</span>
    </div>

    {{-- FOMO countdown banner --}}
    <div
        class="flex flex-wrap items-center justify-between gap-3 px-5 py-6 mb-8 bg-gray-900 text-white rounded-xl overflow-hidden relative"
        x-data="{
            end: null,
            hours: '00', mins: '00', secs: '00',
            expired: false,
            init() {
                const randomHours = 16 + Math.floor(Math.random() * 9); // 16–24
                this.end = new Date(Date.now() + randomHours * 3600 * 1000);
                this.tick();
                setInterval(() => this.tick(), 1000);
            },
            tick() {
                const diff = this.end - new Date();
                if (diff <= 0) { this.expired = true; return; }
                this.hours = String(Math.floor(diff / 3600000)).padStart(2, '0');
                this.mins  = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
                this.secs  = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
            }
        }"
        x-init="init()"
    >
<div class="flex items-center gap-3 relative z-10">
            <svg class="w-5 h-5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm font-bold leading-tight">Your cart prices are reserved!</p>
                <p class="text-sm text-gray-400 mt-0.5">Complete your order before the timer runs out.</p>
            </div>
        </div>

        <div x-show="!expired" class="flex items-center gap-1.5 relative z-10 shrink-0">
            <template x-for="(val, i) in [hours, mins, secs]" :key="i">
                <div class="flex items-center gap-1.5">
                    <span x-text="val"
                          class="inline-flex items-center justify-center w-10 h-9 text-base font-black tabular-nums bg-white/10 rounded-lg"></span>
                    <span x-show="i < 2" class="text-gray-400 font-bold text-sm">:</span>
                </div>
            </template>
            <span class="ml-1 text-xs font-semibold uppercase tracking-widest text-gray-400">remaining</span>
        </div>
        <p x-show="expired" class="text-sm font-bold text-amber-400 relative z-10 shrink-0">Prices may have changed — please review.</p>
    </div>

    @if($errors->any())
        <div class="p-4 mb-8 text-sm text-red-700 border border-red-200 rounded-lg bg-red-50">
            <ul class="pl-5 list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-12 lg:grid-cols-12">
        
        <div class="lg:col-span-8">
            <form action="{{ route('checkout.store') }}" method="POST">
                @csrf
                
                @php
                    $prefillName     = old('name',        auth()->user()->name  ?? $lastOrder->name     ?? '');
                    $prefillEmail    = old('email',       auth()->user()->email ?? $lastOrder->email    ?? '');
                    $prefillAddress  = old('address',     $lastOrder->address  ?? '');
                    $prefillDivision = old('division',    $lastOrder->division ?? 'Dhaka');
                    $prefillDistrict = old('district',    $lastOrder->district ?? '');
                    $prefillPostal   = old('postal_code', $lastOrder->postal_code ?? '');
                    // Strip the +880 prefix stored in DB so the input only shows the local part
                    $rawPhone = $lastOrder->phone ?? '';
                    $prefillPhone = old('phone', preg_replace('/^\+?880/', '', $rawPhone));
                @endphp

                <h2 class="mb-6 text-2xl font-bold text-foreground">Address Details</h2>

                @auth
                    @if($savedAddresses->isNotEmpty())
                        <div class="mb-6">
                            <label class="block mb-1.5 text-sm font-semibold text-foreground">
                                Use a Saved Address
                            </label>
                            <div class="flex items-center gap-2">
                                <select
                                    class="flex-1 px-4 py-2.5 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg"
                                    @change="
                                        if ($event.target.value) {
                                            fillAddress(JSON.parse($event.target.value));
                                        }
                                    ">
                                    <option value="">— Choose a saved address —</option>
                                    @foreach($savedAddresses as $addr)
                                        <option value="{{ json_encode(['name' => $addr->name, 'email' => $addr->email, 'address' => $addr->address, 'division' => $addr->division, 'district' => $addr->district, 'postal_code' => $addr->postal_code, 'phone' => $addr->phone]) }}">
                                            {{ $addr->label }}{{ $addr->is_default ? ' (default)' : '' }} — {{ $addr->address }}
                                        </option>
                                    @endforeach
                                </select>
                                <a href="{{ route('addresses.index') }}"
                                   class="shrink-0 px-3 py-2.5 text-xs font-medium text-muted-foreground border border-border rounded-lg hover:text-foreground hover:bg-muted transition-colors">
                                    Manage
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="mb-6">
                            <p class="text-sm text-muted-foreground">
                                No saved addresses yet.
                                <a href="{{ route('addresses.index') }}" class="font-semibold text-gray-700 hover:underline">Add one</a>
                                to speed up checkout next time.
                            </p>
                        </div>
                    @endif
                @endauth

                @auth
                    @if($lastOrder)
                        <div class="flex items-center gap-2 px-4 py-2 mb-6 text-sm font-medium text-foreground border border-border rounded-lg bg-muted">
                            <svg class="w-4 h-4 shrink-0 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/></svg>
                            We pre-filled your details from your last order. Feel free to update anything.
                        </div>
                    @endif
                @endauth

                <div class="mb-10 space-y-4">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-foreground">Full Name</label>
                        <input type="text" name="name" x-ref="fieldName" value="{{ $prefillName }}" placeholder="Enter your full name" class="w-full px-4 py-3 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)]" required>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-foreground">Email Address (For Order Updates)</label>
                        <input type="email" name="email" x-ref="fieldEmail" value="{{ $prefillEmail }}" placeholder="guest@example.com" class="w-full px-4 py-3 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)]" required>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-foreground">Detailed Address</label>
                        <input type="text" name="address" x-ref="fieldAddress" value="{{ $prefillAddress }}" placeholder="House #, Street, Apartment..." class="w-full px-4 py-3 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)]" required>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-foreground">Division</label>
                            <select name="division" x-model="division" class="w-full px-4 py-3 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)]" required>
                                @foreach(['Dhaka','Chattogram','Sylhet','Rajshahi','Khulna','Barishal','Rangpur','Mymensingh'] as $div)
                                    <option value="{{ $div }}">{{ $div }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-foreground">District</label>
                            <input type="text" name="district" x-ref="fieldDistrict" value="{{ $prefillDistrict }}" placeholder="e.g. Dhaka City" class="w-full px-4 py-3 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)]" required>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-foreground">Postal Code</label>
                            <input type="text" name="postal_code" x-ref="fieldPostal" value="{{ $prefillPostal }}" placeholder="Enter Code" class="w-full px-4 py-3 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)]">
                        </div>
                    </div>

                    <div class="w-full md:w-1/2"
                         x-data="{ phone: '{{ $prefillPhone }}', get valid() { return /^[0-9]{10}$/.test(this.phone); }, get touched() { return this.phone.length > 0; } }">
                        <label class="block mb-1 text-sm font-medium text-foreground">Phone Number</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 font-bold text-muted-foreground border border-r-0 border-input rounded-l-lg bg-muted">+880</span>
                            <input type="text" name="phone" x-ref="fieldPhone" x-model="phone"
                                   placeholder="1XXXXXXXXX"
                                   maxlength="10"
                                   inputmode="numeric"
                                   :class="touched && !valid ? 'border-red-400 focus:ring-red-400' : 'border-input focus:ring-ring'"
                                   class="w-full px-4 py-3 bg-background border text-foreground rounded-r-lg transition-colors focus:ring-2 focus:outline-none"
                                   required>
                        </div>
                        <p x-show="touched && !valid" x-transition
                           class="mt-1 text-xs font-medium text-red-500">
                            Enter exactly 10 digits after +880 &mdash; e.g. <strong>1712345678</strong>
                        </p>
                        <p x-show="!touched || valid"
                           class="mt-1 text-xs text-muted-foreground">
                            Format: <strong>+880</strong> followed by 10 digits (e.g. +880&nbsp;1712345678)
                        </p>
                    </div>
                </div>

                <h2 class="mb-6 text-2xl font-bold text-foreground">Delivery</h2>
                <div class="mb-10 flex items-center justify-between gap-4 p-4 border border-border rounded-xl bg-muted/40">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-background border border-border shrink-0">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-foreground"
                               x-text="division === 'Dhaka' ? 'Inside Dhaka' : 'Outside Dhaka'"></p>
                            <p class="text-sm text-muted-foreground"
                               x-text="division === 'Dhaka' ? '3–5 business days' : '5–7 business days'"></p>
                        </div>
                    </div>
                    <div class="shrink-0 text-right">
                        <p class="text-lg font-bold text-foreground"
                           x-text="shipping === 0 ? 'Free' : '৳ ' + shipping"></p>
                        <p x-show="division === 'Dhaka' && subtotal >= 1500"
                           class="text-xs font-semibold text-emerald-600 mt-0.5">
                            Free for orders ≥ ৳1,500 in Dhaka
                        </p>
                    </div>
                </div>

                <h2 class="mb-6 text-2xl font-bold text-foreground">Payment Method</h2>
                <div class="p-6 mb-8 border border-border rounded-xl bg-muted" x-data="{ payment: 'cod' }">
                    <div class="flex flex-wrap gap-6 mb-4">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" name="payment" value="cod" x-model="payment" class="w-4 h-4 text-gray-700 focus:ring-gray-500">
                            <span class="font-medium text-foreground">Cash on Delivery</span>
                        </label>
                        @if(!empty($bkashNumber))
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" name="payment" value="bkash" x-model="payment" class="w-4 h-4 text-gray-700 focus:ring-gray-500">
                            <span class="font-medium text-foreground">Bkash</span>
                        </label>
                        @endif
                    </div>

                    <p x-show="payment === 'cod'" class="text-xs text-muted-foreground">Pay with cash when your books arrive at your doorstep.</p>

                    @if(!empty($bkashNumber))
                    <div x-show="payment === 'bkash'"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         style="display:none;"
                         class="mt-2 space-y-4">
                        <div class="p-4 border border-pink-200 rounded-lg bg-pink-50">
                            <p class="mb-1 text-xs font-bold tracking-wide text-pink-700 uppercase">Send payment via Bkash</p>
                            <p class="text-sm text-pink-800">
                                Send the exact amount to:
                                <span class="font-black text-lg tracking-wider">{{ $bkashNumber }}</span>
                            </p>
                            <p class="mt-1 text-xs text-pink-600">Please send the payment before placing the order, then enter the Transaction ID below.</p>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-foreground">Bkash Transaction ID</label>
                            <input type="text" name="bkash_transaction_id"
                                   value="{{ old('bkash_transaction_id') }}"
                                   placeholder="e.g. 8A7X6B5C4D"
                                   class="w-full px-4 py-3 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)]">
                            @error('bkash_transaction_id')
                                <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    @endif
                </div>

                <div class="flex items-center mb-8 space-x-2">
                    <input type="checkbox" id="terms" class="w-4 h-4 border-input rounded text-primary focus:ring-ring" required>
                    <label for="terms" class="text-sm text-muted-foreground">I accept the terms of <a href="#" class="underline text-gray-700">Privacy Policy</a></label>
                </div>

                {{-- Hidden coin_reward_id — only submitted when user applies a reward --}}
                <template x-if="applyReward && activeReward">
                    <input type="hidden" name="coin_reward_id" :value="activeReward.id">
                </template>

                {{-- Coupon code — bound to the shared Alpine couponCode variable --}}
                <input type="hidden" name="coupon_code" :value="couponCode">

                <button type="submit" class="w-full px-12 py-4 font-bold transition bg-primary text-primary-foreground rounded-lg shadow-sm hover:bg-primary/90 md:w-auto">
                    Confirm Order
                </button>
            </form>
        </div>

        <div class="lg:col-span-4">
            <div class="sticky p-6 border border-border bg-muted rounded-xl top-6">
                <h3 class="mb-6 text-xl font-bold text-foreground">Order Summary</h3>
                
                @if(isset($cartItems) && count($cartItems) > 0)
                    <div class="space-y-4 mb-6 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($cartItems as $id => $item)
                        <div class="flex items-center p-3 bg-background border border-border rounded-lg shadow-sm">
                            <div class="flex items-center justify-center w-12 h-16 overflow-hidden bg-muted rounded shrink-0">
                                @if(isset($item['image_path']) && $item['image_path'])
                                    <img src="{{ asset('storage/' . $item['image_path']) }}" class="object-cover w-full h-full">
                                @else
                                    <span class="text-[10px] text-muted-foreground">Cover</span>
                                @endif
                            </div>
                            <div class="flex-1 ml-4">
                                <h4 class="text-sm font-bold text-foreground line-clamp-1" title="{{ $item['title'] }}">{{ $item['title'] }}</h4>
                                
                                <div class="flex items-center mt-1 space-x-2">
                                    <form action="{{ route('cart.decrement', $id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="flex items-center justify-center w-5 h-5 text-muted-foreground transition bg-muted rounded hover:bg-muted hover:opacity-80 hover:text-gray-700">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                        </button>
                                    </form>

                                    <span class="text-xs font-bold text-foreground">{{ $item['quantity'] }}</span>

                                    <form action="{{ route('cart.increment', $id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="flex items-center justify-center w-5 h-5 text-muted-foreground transition bg-muted rounded hover:bg-muted hover:opacity-80 hover:text-gray-500">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="flex items-center ml-4 space-x-3">
                                <span class="text-sm font-bold text-foreground">৳ {{ number_format($item['price'] * $item['quantity'], 0) }}</span>
                                
                                <form action="{{ route('cart.remove', $id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-muted-foreground transition hover:text-destructive">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="pt-4 space-y-3 border-t border-border">
                        <div class="flex justify-between text-sm text-muted-foreground">
                            <span>Subtotal</span>
                            <span class="font-medium text-foreground">৳ {{ number_format($subtotal, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-muted-foreground">
                            <span>Shipping</span>
                            <span class="font-medium"
                                  :class="shipping === 0 ? 'text-emerald-600' : 'text-gray-700'"
                                  x-text="shipping === 0 ? 'Free' : '+৳ ' + shipping"></span>
                        </div>

                        {{-- Coupon discount row --}}
                        <div
                            x-show="couponDiscount > 0"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="flex justify-between text-sm font-semibold text-green-700"
                        >
                            <span>Discount (<span x-text="couponCode.toUpperCase()"></span>)</span>
                            <span>−৳ <span x-text="couponDiscount.toLocaleString()"></span></span>
                        </div>

                        {{-- Coin shipping reward row --}}
                        <div
                            x-show="applyReward && rewardDiscount > 0"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="flex justify-between text-sm font-semibold text-amber-700"
                            style="display:none;"
                        >
                            <span>Shipping Reward (Coin Redemption)</span>
                            <span>−৳ <span x-text="rewardDiscount.toLocaleString()"></span></span>
                        </div>

                        {{-- Discount code — logged-in users only ────────────── --}}
                        @auth
                        <div class="pt-3 mt-1 border-t border-dashed border-border">
                            <label class="block mb-1.5 text-xs font-semibold text-foreground">
                                Discount Code
                            </label>
                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    x-model="couponCode"
                                    @keydown.enter.prevent="applyVoucher()"
                                    placeholder="Enter code..."
                                    maxlength="50"
                                    style="text-transform:uppercase"
                                    class="flex-1 px-3 py-2.5 text-sm font-mono uppercase tracking-widest bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg transition-colors"
                                >
                                <button type="button" @click="applyVoucher()"
                                        :disabled="couponChecking"
                                        class="px-3 py-2.5 text-xs font-bold bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors shrink-0 disabled:opacity-50">
                                    <span x-show="!couponChecking">Apply</span>
                                    <span x-show="couponChecking" x-cloak>...</span>
                                </button>
                            </div>
                            <p x-show="couponValid === true" x-cloak class="mt-1.5 text-xs font-medium text-green-600">
                                ✓ <span x-text="couponMessage"></span>
                            </p>
                            <p x-show="couponValid === false" x-cloak class="mt-1.5 text-xs font-medium text-red-500">
                                <span x-text="couponMessage"></span>
                            </p>
                            @error('coupon_code')
                                <p class="mt-1.5 text-xs font-medium text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        @endauth
                        {{-- ─────────────────────────────────────────────────── --}}

                        {{-- Coin shipping reward toggle (authenticated users only) --}}
                        @auth
                        <div x-show="activeReward" style="display:none;" class="pt-3 mt-1 border-t border-dashed border-border">
                            <label class="flex items-start gap-3 cursor-pointer group">
                                <div class="relative mt-0.5 shrink-0">
                                    <input type="checkbox" x-model="applyReward" class="sr-only peer">
                                    <div class="w-10 h-6 bg-muted rounded-full transition-colors peer-checked:bg-emerald-500"></div>
                                    <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-foreground group-hover:text-emerald-700 transition-colors">
                                        Apply Shipping Reward
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        You have a
                                        <span class="font-bold text-emerald-600">৳<span x-text="activeReward ? activeReward.shipping_discount : 0"></span> off shipping</span>
                                        reward ready to use.
                                    </p>
                                </div>
                            </label>
                        </div>
                        @endauth

                        <div class="flex justify-between pt-4 mt-2 text-xl font-bold text-foreground border-t border-border">
                            <span>Total</span>
                            <span class="text-gray-800">৳ <span x-text="grandTotal.toLocaleString()"></span></span>
                        </div>
                    </div>
                @else
                    <div class="py-8 text-center">
                        <p class="text-muted-foreground">Cart is empty.</p>
                        <a href="/categories" class="inline-block mt-2 text-sm font-bold text-gray-700">Browse Books</a>
                    </div>
                @endif
            </div>
        </div>
        
    </div>
</div>
@endsection