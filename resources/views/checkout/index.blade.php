@extends('layouts.app')

@section('content')
<div class="container px-4 py-8 mx-auto" x-data="{ shipping: {{ old('delivery', 'express') === 'standard' ? 60 : 150 }} }">
    
    <div class="mb-8 text-sm text-gray-500">
        <a href="/" class="hover:text-orange-500">Home</a> <span class="mx-2">></span> 
        <a href="/categories" class="hover:text-orange-500">Categories</a> <span class="mx-2">></span> 
        <span class="text-gray-900">Checkout</span>
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

                <h2 class="mb-6 text-2xl font-bold text-gray-900">Address Details</h2>

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
                        <label class="block mb-1 text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" name="name" value="{{ $prefillName }}" placeholder="Enter your full name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500" required>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Email Address (For Order Updates)</label>
                        <input type="email" name="email" value="{{ $prefillEmail }}" placeholder="guest@example.com" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500" required>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Detailed Address</label>
                        <input type="text" name="address" value="{{ $prefillAddress }}" placeholder="House #, Street, Apartment..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500" required>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Division</label>
                            <select name="division" class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500" required>
                                @foreach(['Dhaka','Chittagong','Sylhet','Rajshahi','Khulna','Barisal','Rangpur','Mymensingh'] as $div)
                                    <option value="{{ $div }}" {{ $prefillDivision === $div ? 'selected' : '' }}>{{ $div }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">District</label>
                            <input type="text" name="district" value="{{ $prefillDistrict }}" placeholder="e.g. Dhaka City" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500" required>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Postal Code</label>
                            <input type="text" name="postal_code" value="{{ $prefillPostal }}" placeholder="Enter Code" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500">
                        </div>
                    </div>

                    <div class="w-full md:w-1/2"
                         x-data="{ phone: '{{ $prefillPhone }}', get valid() { return /^[0-9]{10}$/.test(this.phone); }, get touched() { return this.phone.length > 0; } }">
                        <label class="block mb-1 text-sm font-medium text-gray-700">Phone Number</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 font-bold text-gray-500 border border-r-0 border-gray-300 rounded-l-lg bg-gray-50">+880</span>
                            <input type="text" name="phone" x-model="phone"
                                   placeholder="1XXXXXXXXX"
                                   maxlength="10"
                                   inputmode="numeric"
                                   :class="touched && !valid ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-300 focus:ring-cyan-500 focus:border-cyan-500'"
                                   class="w-full px-4 py-3 border rounded-r-lg transition-colors"
                                   required>
                        </div>
                        <p x-show="touched && !valid" x-transition
                           class="mt-1 text-xs font-medium text-red-500">
                            Enter exactly 10 digits after +880 &mdash; e.g. <strong>1712345678</strong>
                        </p>
                        <p x-show="!touched || valid"
                           class="mt-1 text-xs text-gray-400">
                            Format: <strong>+880</strong> followed by 10 digits (e.g. +880&nbsp;1712345678)
                        </p>
                    </div>
                </div>

                <h2 class="mb-6 text-2xl font-bold text-gray-900">Delivery Method</h2>
                <div class="grid grid-cols-1 gap-4 mb-10 md:grid-cols-2">
                    <label class="relative flex flex-col items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="delivery" value="standard" @click="shipping = 60" {{ old('delivery') == 'standard' ? 'checked' : '' }} class="absolute w-0 h-0 opacity-0 peer">
                        <span class="font-bold text-gray-900">Standard Delivery</span>
                        <span class="text-sm text-gray-500">(3-5 days)</span>
                        <span class="mt-2 font-bold text-gray-900">৳ 60</span>
                        <div class="absolute inset-0 transition-all border-2 border-transparent rounded-lg peer-checked:border-cyan-500"></div>
                    </label>
                    
                    <label class="relative flex flex-col items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="delivery" value="express" @click="shipping = 150" {{ old('delivery', 'express') == 'express' ? 'checked' : '' }} class="absolute w-0 h-0 opacity-0 peer">
                        <span class="font-bold text-gray-900">Express Delivery</span>
                        <span class="text-sm text-gray-500">(1-2 days)</span>
                        <span class="mt-2 font-bold text-gray-900">৳ 150</span>
                        <div class="absolute inset-0 transition-all border-2 border-transparent rounded-lg peer-checked:border-cyan-500"></div>
                    </label>
                </div>

                <h2 class="mb-6 text-2xl font-bold text-gray-900">Payment Method</h2>
                <div class="p-6 mb-8 border border-gray-200 rounded-xl bg-gray-50">
                    <div class="flex flex-wrap gap-6 mb-6">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" name="payment" value="cod" checked class="w-4 h-4 text-cyan-600 focus:ring-cyan-500">
                            <span class="font-medium text-gray-900">Cash on Delivery</span>
                        </label>
                    </div>
                    <p class="text-xs text-gray-500">Pay with cash when your books arrive at your doorstep.</p>
                </div>

                <div class="flex items-center mb-8 space-x-2">
                    <input type="checkbox" id="terms" class="w-4 h-4 border-gray-300 rounded text-cyan-600 focus:ring-cyan-500" required>
                    <label for="terms" class="text-sm text-gray-600">I accept the terms of <a href="#" class="underline text-cyan-600">Privacy Policy</a></label>
                </div>

                <button type="submit" class="w-full px-12 py-4 font-bold text-white transition bg-orange-500 rounded-lg shadow-lg hover:bg-orange-600 md:w-auto">
                    Confirm Order
                </button>
            </form>
        </div>

        <div class="lg:col-span-4">
            <div class="sticky p-6 border border-gray-200 bg-gray-50 rounded-xl top-6">
                <h3 class="mb-6 text-xl font-bold text-gray-900">Order Summary</h3>
                
                @if(isset($cartItems) && count($cartItems) > 0)
                    <div class="space-y-4 mb-6 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($cartItems as $id => $item)
                        <div class="flex items-center p-3 bg-white border border-gray-100 rounded-lg shadow-sm">
                            <div class="flex items-center justify-center w-12 h-16 overflow-hidden bg-gray-200 rounded shrink-0">
                                @if(isset($item['image_path']) && $item['image_path'])
                                    <img src="{{ asset('storage/' . $item['image_path']) }}" class="object-cover w-full h-full">
                                @else
                                    <span class="text-[10px] text-gray-400">Cover</span>
                                @endif
                            </div>
                            <div class="flex-1 ml-4">
                                <h4 class="text-sm font-bold text-gray-900 line-clamp-1" title="{{ $item['title'] }}">{{ $item['title'] }}</h4>
                                
                                <div class="flex items-center mt-1 space-x-2">
                                    <form action="{{ route('cart.decrement', $id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="flex items-center justify-center w-5 h-5 text-gray-500 transition bg-gray-100 rounded hover:bg-gray-200 hover:text-orange-500">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                        </button>
                                    </form>

                                    <span class="text-xs font-bold text-gray-700">{{ $item['quantity'] }}</span>

                                    <form action="{{ route('cart.increment', $id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="flex items-center justify-center w-5 h-5 text-gray-500 transition bg-gray-100 rounded hover:bg-gray-200 hover:text-cyan-500">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="flex items-center ml-4 space-x-3">
                                <span class="text-sm font-bold text-gray-900">৳ {{ number_format($item['price'] * $item['quantity'], 0) }}</span>
                                
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

                    <div class="pt-4 space-y-3 border-t border-gray-200">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-medium text-gray-900">৳ {{ number_format($subtotal, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Shipping</span>
                            <span class="font-medium text-cyan-600">+৳ <span x-text="shipping"></span></span>
                        </div>
                        <div class="flex justify-between pt-4 mt-2 text-xl font-bold text-gray-900 border-t border-gray-200">
                            <span>Total</span>
                            <span class="text-orange-600">৳ <span x-text="{{ $subtotal }} + shipping"></span></span>
                        </div>
                    </div>
                @else
                    <div class="py-8 text-center">
                        <p class="text-gray-500">Cart is empty.</p>
                        <a href="/categories" class="inline-block mt-2 text-sm font-bold text-cyan-600">Browse Books</a>
                    </div>
                @endif
            </div>
        </div>
        
    </div>
</div>
@endsection