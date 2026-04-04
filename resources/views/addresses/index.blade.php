@extends('layouts.dashboard')

@section('page-title', 'Saved Addresses')

@section('dashboard-content')

@if(session('success'))
    <div class="p-4 text-sm font-medium text-green-700 border border-green-200 rounded-xl bg-green-50">
        {{ session('success') }}
    </div>
@endif

{{-- Address Cards --}}
@if($addresses->isNotEmpty())
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        @foreach($addresses as $addr)
        <div class="relative p-5 bg-card text-card-foreground border rounded-2xl shadow-sm {{ $addr->is_default ? 'border-cyan-300 ring-1 ring-cyan-200' : 'border-border' }}"
             x-data="{ editOpen: false }" 
             @keydown.escape.window="if(editOpen) { editOpen = false; document.body.style.overflow = 'auto'; }">

            {{-- Default badge --}}
            @if($addr->is_default)
                <span class="absolute top-4 right-4 text-[10px] font-bold tracking-wider uppercase text-cyan-700 bg-cyan-100 px-2 py-0.5 rounded-full">Default</span>
            @endif

            <div class="flex items-center gap-2 mb-3">
                <span class="text-xs font-bold tracking-wider uppercase text-muted-foreground bg-muted px-2.5 py-1 rounded-full">{{ $addr->label }}</span>
            </div>

            <p class="font-bold text-foreground">{{ $addr->name }}</p>
            <p class="text-sm text-muted-foreground mt-0.5">{{ $addr->address }}</p>
            <p class="text-sm text-muted-foreground">{{ $addr->district }}, {{ $addr->division }} {{ $addr->postal_code }}</p>
            <p class="mt-1 text-sm text-muted-foreground">+880 {{ $addr->phone }}</p>

            <div class="flex flex-wrap items-center gap-2 pt-4 mt-4 border-t border-border">
                @if(!$addr->is_default)
                    <form action="{{ route('addresses.default', $addr) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-xs font-semibold transition-colors text-cyan-700 hover:text-cyan-900">
                            Set as Default
                        </button>
                    </form>
                    <span class="text-gray-300">|</span>
                @endif

                <button @click="editOpen = true; document.body.style.overflow = 'hidden';" class="text-xs font-semibold transition-colors text-muted-foreground hover:text-foreground">
                    Edit
                </button>
                <span class="text-gray-300">|</span>

                <form action="{{ route('addresses.destroy', $addr) }}" method="POST"
                      onsubmit="return confirm('Remove this address?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-semibold text-red-500 transition-colors hover:text-red-700">
                        Remove
                    </button>
                </form>
            </div>

            {{-- Edit Modal --}}
            <template x-teleport="body">
                <div x-show="editOpen" x-transition
                     class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/40"
                     style="display:none">
                    <div class="w-full max-w-lg bg-white text-card-foreground rounded-2xl shadow-xl p-6 max-h-[90vh] overflow-y-auto"
                         @click.outside="editOpen = false; document.body.style.overflow = 'auto';">
                        <h3 class="mb-5 text-lg font-bold text-foreground">Edit Address</h3>

                        <form action="{{ route('addresses.update', $addr) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block mb-1 text-sm font-medium text-foreground">Label</label>
                                <input type="text" name="label" value="{{ $addr->label }}" placeholder="e.g. Home, Work"
                                       class="w-full px-4 py-2.5 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg" required>
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-foreground">Full Name</label>
                                <input type="text" name="name" value="{{ $addr->name }}"
                                       class="w-full px-4 py-2.5 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg" required>
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-foreground">Email</label>
                                <input type="email" name="email" value="{{ $addr->email }}"
                                       class="w-full px-4 py-2.5 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg" required>
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-foreground">Detailed Address</label>
                                <input type="text" name="address" value="{{ $addr->address }}"
                                       class="w-full px-4 py-2.5 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg" required>
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block mb-1 text-sm font-medium text-foreground">Division</label>
                                    <select name="division" class="w-full px-3 py-2.5 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg" required>
                                        @foreach(['Dhaka','Chittagong','Sylhet','Rajshahi','Khulna','Barisal','Rangpur','Mymensingh'] as $div)
                                            <option value="{{ $div }}" {{ $addr->division === $div ? 'selected' : '' }}>{{ $div }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block mb-1 text-sm font-medium text-foreground">District</label>
                                    <input type="text" name="district" value="{{ $addr->district }}"
                                           class="w-full px-3 py-2.5 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg" required>
                                </div>
                                <div>
                                    <label class="block mb-1 text-sm font-medium text-foreground">Postal Code</label>
                                    <input type="text" name="postal_code" value="{{ $addr->postal_code }}"
                                           class="w-full px-3 py-2.5 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg">
                                </div>
                            </div>
                            <div x-data="{ phone: '{{ $addr->phone }}', get valid() { return /^[0-9]{10}$/.test(this.phone); } }">
                                <label class="block mb-1 text-sm font-medium text-foreground">Phone</label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 font-bold border border-r-0 rounded-l-lg text-muted-foreground border-input bg-muted">+880</span>
                                    <input type="text" name="phone" x-model="phone" maxlength="10" inputmode="numeric"
                                           :class="phone.length > 0 && !valid ? 'border-red-400 focus:ring-red-400' : 'border-input focus:ring-ring'"
                                           class="w-full px-4 py-2.5 bg-background border text-foreground focus:ring-2 focus:outline-none rounded-r-lg transition-colors" required>
                                </div>
                                <p x-show="phone.length > 0 && !valid" class="mt-1 text-xs text-red-500">Enter exactly 10 digits after +880</p>
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_default" value="1" {{ $addr->is_default ? 'checked' : '' }}
                                       class="w-4 h-4 rounded text-cyan-600 border-input focus:ring-2 focus:ring-ring">
                                <span class="text-sm font-medium text-foreground">Set as default address</span>
                            </label>

                            <div class="flex justify-end gap-3 pt-2">
                                <button type="button" @click="editOpen = false; document.body.style.overflow = 'auto';"
                                        class="px-4 py-2 text-sm font-semibold transition-colors border rounded-lg text-foreground bg-background border-border hover:bg-muted">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="px-5 py-2 text-sm font-bold text-white transition-colors rounded-lg bg-cyan-600 hover:bg-cyan-700">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        </div>
        @endforeach
    </div>
@else
    <div class="py-16 text-center bg-white border shadow-sm text-card-foreground border-border rounded-2xl">
        <div class="inline-flex items-center justify-center w-16 h-16 mb-5 rounded-full text-cyan-500 bg-cyan-50">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <h2 class="mb-2 text-xl font-bold text-foreground">No saved addresses yet</h2>
        <p class="text-muted-foreground">Save an address below to speed up checkout.</p>
    </div>
@endif

{{-- Add Address --}}
<div x-data="{ open: false }" @keydown.escape.window="if(open) { open = false; document.body.style.overflow = 'auto'; }">
    <button @click="open = true; document.body.style.overflow = 'hidden';"
            class="flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white bg-cyan-600 rounded-xl hover:bg-cyan-700 transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add New Address
    </button>

    <template x-teleport="body">
    <div x-show="open" x-transition
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/40"
         style="display:none">
        <div class="w-full max-w-lg bg-white text-card-foreground rounded-2xl shadow-xl p-6 max-h-[90vh] overflow-y-auto"
             @click.outside="open = false; document.body.style.overflow = 'auto';">
            <h3 class="mb-5 text-lg font-bold text-foreground">Add New Address</h3>

            <form action="{{ route('addresses.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block mb-1 text-sm font-medium text-foreground">Label</label>
                    <input type="text" name="label" value="{{ old('label') }}" placeholder="e.g. Home, Work, Other"
                           class="w-full px-4 py-2.5 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg" required>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium text-foreground">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                           class="w-full px-4 py-2.5 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg" required>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium text-foreground">Email</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                           class="w-full px-4 py-2.5 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg" required>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium text-foreground">Detailed Address</label>
                    <input type="text" name="address" value="{{ old('address') }}" placeholder="House #, Street, Apartment..."
                           class="w-full px-4 py-2.5 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg" required>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-foreground">Division</label>
                        <select name="division" class="w-full px-3 py-2.5 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg" required>
                            @foreach(['Dhaka','Chittagong','Sylhet','Rajshahi','Khulna','Barisal','Rangpur','Mymensingh'] as $div)
                                <option value="{{ $div }}" {{ old('division', 'Dhaka') === $div ? 'selected' : '' }}>{{ $div }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-foreground">District</label>
                        <input type="text" name="district" value="{{ old('district') }}" placeholder="e.g. Dhaka City"
                               class="w-full px-3 py-2.5 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg" required>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-foreground">Postal Code</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code') }}"
                               class="w-full px-3 py-2.5 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg">
                    </div>
                </div>
                <div x-data="{ phone: '{{ old('phone') }}', get valid() { return /^[0-9]{10}$/.test(this.phone); } }">
                    <label class="block mb-1 text-sm font-medium text-foreground">Phone</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 font-bold border border-r-0 rounded-l-lg text-muted-foreground border-input bg-muted">+880</span>
                        <input type="text" name="phone" x-model="phone" placeholder="1XXXXXXXXX" maxlength="10" inputmode="numeric"
                               :class="phone.length > 0 && !valid ? 'border-red-400 focus:ring-red-400' : 'border-input focus:ring-ring'"
                               class="w-full px-4 py-2.5 bg-background border text-foreground focus:ring-2 focus:outline-none rounded-r-lg transition-colors" required>
                    </div>
                    <p x-show="phone.length > 0 && !valid" class="mt-1 text-xs text-red-500">Enter exactly 10 digits after +880</p>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_default" value="1"
                           class="w-4 h-4 rounded text-cyan-600 border-input focus:ring-2 focus:ring-ring">
                    <span class="text-sm font-medium text-foreground">Set as default address</span>
                </label>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="open = false; document.body.style.overflow = 'auto';"
                            class="px-4 py-2 text-sm font-semibold transition-colors border rounded-lg text-foreground bg-background border-border hover:bg-muted">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2 text-sm font-bold text-white transition-colors rounded-lg bg-cyan-600 hover:bg-cyan-700">
                        Save Address
                    </button>
                </div>
            </form>
        </div>
    </div>
    </template>
</div>

@endsection