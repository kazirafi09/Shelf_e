@extends('layouts.app')

@section('content')
<div class="container px-4 py-8 mx-auto" x-data="{
    shipping: {{ old('delivery', 'express') === 'standard' ? 60 : 150 }},
    subtotal: {{ $subtotal }},
    availableCoins: {{ auth()->check() ? auth()->user()->coin_balance : 0 }},
    redeemCoins: false,
    get coinsToApply() {
        return Math.min(this.availableCoins, this.subtotal + this.shipping);
    },
    get grandTotal() {
        return this.subtotal + this.shipping - (this.redeemCoins ? this.coinsToApply : 0);
    },
    fillAddress(addr) {
        this.$refs.fieldName.value      = addr.name;
        this.$refs.fieldEmail.value     = addr.email;
        this.$refs.fieldAddress.value   = addr.address;
        this.$refs.fieldDivision.value  = addr.division;
        this.$refs.fieldDistrict.value  = addr.district;
        this.$refs.fieldPostal.value    = addr.postal_code ?? '';
        this.$refs.fieldPhone.value     = addr.phone;
        this.$refs.fieldPhone.dispatchEvent(new Event('input'));
    }
}">
    
    <div class="mb-8 text-sm text-muted-foreground">
        <a href="/" class="hover:text-orange-500">Home</a> <span class="mx-2">></span>
        <a href="/categories" class="hover:text-orange-500">Categories</a> <span class="mx-2">></span>
        <span class="text-foreground">Checkout</span>
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
                            <p class="mb-2 text-sm font-semibold text-muted-foreground">Autofill from saved address:</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($savedAddresses as $addr)
                                    <button type="button"
                                            @click="fillAddress({{ json_encode(['name' => $addr->name, 'email' => $addr->email, 'address' => $addr->address, 'division' => $addr->division, 'district' => $addr->district, 'postal_code' => $addr->postal_code, 'phone' => $addr->phone]) }})"
                                            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-cyan-700 bg-cyan-50 border border-cyan-200 rounded-lg hover:bg-cyan-100 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $addr->label }}
                                        @if($addr->is_default)
                                            <span class="text-[10px] text-cyan-500">(default)</span>
                                        @endif
                                    </button>
                                @endforeach
                                <a href="{{ route('addresses.index') }}" class="flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-muted-foreground border border-border rounded-lg hover:text-foreground hover:bg-muted transition-colors">
                                    Manage
                                </a>
                            </div>
                        </div>
                    @endif
                @endauth

                @auth
                    @if($lastOrder)
                        <div class="flex items-center gap-2 px-4 py-2 mb-6 text-sm font-medium text-cyan-800 border border-cyan-200 rounded-lg bg-cyan-50">
                            <svg class="w-4 h-4 shrink-0 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/></svg>
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
                            <select name="division" x-ref="fieldDivision" class="w-full px-4 py-3 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)]" required>
                                @foreach(['Dhaka','Chittagong','Sylhet','Rajshahi','Khulna','Barisal','Rangpur','Mymensingh'] as $div)
                                    <option value="{{ $div }}" {{ $prefillDivision === $div ? 'selected' : '' }}>{{ $div }}</option>
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

                <h2 class="mb-6 text-2xl font-bold text-foreground">Delivery Method</h2>
                <div class="grid grid-cols-1 gap-4 mb-10 md:grid-cols-2">
                    <label class="relative flex flex-col items-center p-4 border border-border rounded-lg cursor-pointer hover:bg-muted">
                        <input type="radio" name="delivery" value="standard" @click="shipping = 60" {{ old('delivery') == 'standard' ? 'checked' : '' }} class="absolute w-0 h-0 opacity-0 peer">
                        <span class="font-bold text-foreground">Standard Delivery</span>
                        <span class="text-sm text-muted-foreground">(3-5 days)</span>
                        <span class="mt-2 font-bold text-foreground">৳ 60</span>
                        <div class="absolute inset-0 transition-all border-2 border-transparent rounded-lg peer-checked:border-cyan-500"></div>
                    </label>
                    
                    <label class="relative flex flex-col items-center p-4 border border-border rounded-lg cursor-pointer hover:bg-muted">
                        <input type="radio" name="delivery" value="express" @click="shipping = 150" {{ old('delivery', 'express') == 'express' ? 'checked' : '' }} class="absolute w-0 h-0 opacity-0 peer">
                        <span class="font-bold text-foreground">Express Delivery</span>
                        <span class="text-sm text-muted-foreground">(1-2 days)</span>
                        <span class="mt-2 font-bold text-foreground">৳ 150</span>
                        <div class="absolute inset-0 transition-all border-2 border-transparent rounded-lg peer-checked:border-cyan-500"></div>
                    </label>
                </div>

                <h2 class="mb-6 text-2xl font-bold text-foreground">Payment Method</h2>
                <div class="p-6 mb-8 border border-border rounded-xl bg-muted">
                    <div class="flex flex-wrap gap-6 mb-6">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" name="payment" value="cod" checked class="w-4 h-4 text-cyan-600 focus:ring-cyan-500">
                            <span class="font-medium text-foreground">Cash on Delivery</span>
                        </label>
                    </div>
                    <p class="text-xs text-muted-foreground">Pay with cash when your books arrive at your doorstep.</p>
                </div>

                <div class="flex items-center mb-8 space-x-2">
                    <input type="checkbox" id="terms" class="w-4 h-4 border-gray-300 rounded text-cyan-600 focus:ring-cyan-500" required>
                    <label for="terms" class="text-sm text-muted-foreground">I accept the terms of <a href="#" class="underline text-cyan-600">Privacy Policy</a></label>
                </div>

                {{-- Hidden redeem_coins flag — only submitted when the toggle is on --}}
                <template x-if="redeemCoins">
                    <input type="hidden" name="redeem_coins" value="1">
                </template>

                <button type="submit" class="w-full px-12 py-4 font-bold text-white transition bg-orange-500 rounded-lg shadow-lg hover:bg-orange-600 md:w-auto">
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
                                        <button type="submit" class="flex items-center justify-center w-5 h-5 text-muted-foreground transition bg-muted rounded hover:bg-muted hover:opacity-80 hover:text-orange-500">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                        </button>
                                    </form>

                                    <span class="text-xs font-bold text-foreground">{{ $item['quantity'] }}</span>

                                    <form action="{{ route('cart.increment', $id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="flex items-center justify-center w-5 h-5 text-muted-foreground transition bg-muted rounded hover:bg-muted hover:opacity-80 hover:text-cyan-500">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="flex items-center ml-4 space-x-3">
                                <span class="text-sm font-bold text-foreground">৳ {{ number_format($item['price'] * $item['quantity'], 0) }}</span>
                                
                                <form action="{{ route('cart.remove', $id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-gray-400 transition hover:text-red-500">
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
                            <span class="font-medium text-cyan-600">+৳ <span x-text="shipping"></span></span>
                        </div>

                        {{-- Redeem Coins (authenticated users only) --}}
                        @auth
                        <div class="pt-3 mt-1 border-t border-dashed border-border">
                            <div x-show="availableCoins > 0">
                                <label class="flex items-start gap-3 cursor-pointer group">
                                    <div class="relative mt-0.5 shrink-0">
                                        <input type="checkbox" x-model="redeemCoins" class="sr-only peer">
                                        <div class="w-10 h-6 bg-gray-200 rounded-full transition-colors peer-checked:bg-amber-500"></div>
                                        <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-foreground group-hover:text-amber-700 transition-colors">
                                            Redeem Coins
                                        </p>
                                        <p class="text-xs text-muted-foreground">
                                            You have
                                            <span class="font-bold text-amber-600" x-text="availableCoins.toLocaleString()"></span>
                                            coins &mdash; saves
                                            <span class="font-bold text-amber-600" x-text="'৳ ' + coinsToApply.toLocaleString()"></span>
                                        </p>
                                    </div>
                                </label>

                                <div x-show="redeemCoins"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 -translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="flex justify-between mt-2 text-sm"
                                     style="display: none;">
                                    <span class="font-medium text-amber-700">Coin Discount</span>
                                    <span class="font-bold text-amber-700">
                                        −৳ <span x-text="coinsToApply.toLocaleString()"></span>
                                    </span>
                                </div>
                            </div>

                            <div x-show="availableCoins === 0"
                                 class="flex items-start gap-3"
                                 style="display: none;">
                                <div class="relative mt-0.5 shrink-0 opacity-40 cursor-not-allowed">
                                    <div class="w-10 h-6 bg-gray-200 rounded-full"></div>
                                    <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow"></div>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-muted-foreground">Redeem Coins</p>
                                    <p class="text-xs text-muted-foreground">You have 0 coins. Earn coins by placing orders!</p>
                                </div>
                            </div>
                        </div>
                        @endauth

                        <div class="flex justify-between pt-4 mt-2 text-xl font-bold text-foreground border-t border-border">
                            <span>Total</span>
                            <span class="text-orange-600">৳ <span x-text="grandTotal.toLocaleString()"></span></span>
                        </div>
                    </div>
                @else
                    <div class="py-8 text-center">
                        <p class="text-muted-foreground">Cart is empty.</p>
                        <a href="/categories" class="inline-block mt-2 text-sm font-bold text-cyan-600">Browse Books</a>
                    </div>
                @endif
            </div>
        </div>
        
    </div>
</div>
@endsection