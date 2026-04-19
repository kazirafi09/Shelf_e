@extends('layouts.admin')

@section('title', 'New Voucher')
@section('subtitle', 'Create a discount voucher for your customers.')

@section('admin-content')
<div class="max-w-2xl mx-auto">

    @if($errors->any())
        <div class="p-4 mb-6 text-sm text-red-700 bg-red-100 border border-red-200 rounded-xl">
            <ul class="pl-5 list-disc">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.vouchers.store') }}" method="POST"
          class="p-8 bg-card text-card-foreground border border-border shadow-sm rounded-3xl"
          x-data="{ discountType: '{{ old('discount_type', 'percentage') }}' }">
        @csrf

        <div class="space-y-6">

            {{-- Code --}}
            <div>
                <label for="code" class="block mb-1 text-sm font-bold text-foreground">
                    Voucher Code <span class="text-destructive">*</span>
                </label>
                <input type="text" id="code" name="code" value="{{ old('code') }}" required
                       placeholder="e.g. SUMMER20"
                       style="text-transform:uppercase"
                       class="block w-full px-4 py-3 text-sm font-mono uppercase tracking-widest bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm">
                <p class="mt-1 text-xs text-muted-foreground">Letters and numbers only. Will be stored in uppercase.</p>
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block mb-1 text-sm font-bold text-foreground">Description</label>
                <input type="text" id="description" name="description" value="{{ old('description') }}"
                       placeholder="e.g. Eid special offer for all customers"
                       class="block w-full px-4 py-3 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm">
            </div>

            {{-- Discount Type + Value --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="discount_type" class="block mb-1 text-sm font-bold text-foreground">
                        Discount Type <span class="text-destructive">*</span>
                    </label>
                    <select id="discount_type" name="discount_type" x-model="discountType" required
                            class="block w-full px-4 py-3 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm">
                        <option value="percentage" {{ old('discount_type') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Fixed Amount (৳)</option>
                    </select>
                </div>
                <div>
                    <label for="discount_value" class="block mb-1 text-sm font-bold text-foreground">
                        Discount Value <span class="text-destructive">*</span>
                    </label>
                    <div class="relative">
                        <span x-text="discountType === 'percentage' ? '%' : '৳'"
                              class="absolute right-3 top-1/2 -translate-y-1/2 text-sm font-bold text-muted-foreground pointer-events-none"></span>
                        <input type="number" id="discount_value" name="discount_value"
                               value="{{ old('discount_value') }}" required
                               :max="discountType === 'percentage' ? 100 : 99999"
                               min="0.01" step="0.01"
                               class="block w-full px-4 py-3 pr-10 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm">
                    </div>
                    <p x-show="discountType === 'percentage'" class="mt-1 text-xs text-muted-foreground">Max 100%</p>
                </div>
            </div>

            {{-- Max discount (upto) — percentage only --}}
            <div x-show="discountType === 'percentage'" x-cloak>
                <label for="max_discount_amount" class="block mb-1 text-sm font-bold text-foreground">Max Discount (Upto) (৳)</label>
                <div class="relative">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm font-bold text-muted-foreground pointer-events-none">৳</span>
                    <input type="number" id="max_discount_amount" name="max_discount_amount"
                           value="{{ old('max_discount_amount') }}" min="1" step="1" placeholder="No cap"
                           class="block w-full px-4 py-3 pr-10 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm">
                </div>
                <p class="mt-1 text-xs text-muted-foreground">Leave blank for no cap. E.g. 15% off upto ৳100.</p>
            </div>

            {{-- Min order amount --}}
            <div>
                <label for="min_order_amount" class="block mb-1 text-sm font-bold text-foreground">Minimum Order Amount (৳)</label>
                <input type="number" id="min_order_amount" name="min_order_amount"
                       value="{{ old('min_order_amount', 0) }}" min="0" step="1"
                       class="block w-full px-4 py-3 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm">
                <p class="mt-1 text-xs text-muted-foreground">Set to 0 for no minimum.</p>
            </div>

            {{-- Usage limits --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="max_uses" class="block mb-1 text-sm font-bold text-foreground">Total Uses Allowed</label>
                    <input type="number" id="max_uses" name="max_uses"
                           value="{{ old('max_uses') }}" min="1" placeholder="Unlimited"
                           class="block w-full px-4 py-3 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm">
                    <p class="mt-1 text-xs text-muted-foreground">Leave blank for unlimited uses.</p>
                </div>
                <div>
                    <label for="max_uses_per_user" class="block mb-1 text-sm font-bold text-foreground">
                        Uses Per User <span class="text-destructive">*</span>
                    </label>
                    <input type="number" id="max_uses_per_user" name="max_uses_per_user"
                           value="{{ old('max_uses_per_user', 1) }}" min="1" required
                           class="block w-full px-4 py-3 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm">
                </div>
            </div>

            {{-- Expiry date --}}
            <div>
                <label for="expires_at" class="block mb-1 text-sm font-bold text-foreground">Expiry Date</label>
                <input type="datetime-local" id="expires_at" name="expires_at"
                       value="{{ old('expires_at') }}"
                       class="block w-full px-4 py-3 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm">
                <p class="mt-1 text-xs text-muted-foreground">Leave blank for no expiry.</p>
            </div>

            {{-- Toggles --}}
            <div class="flex flex-col gap-4 pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', 1) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-input text-primary focus:ring-ring">
                    <span class="text-sm font-semibold text-foreground">Active</span>
                    <span class="text-xs text-muted-foreground">(inactive vouchers cannot be used at checkout)</span>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="is_announced" value="0">
                    <input type="checkbox" name="is_announced" value="1"
                           {{ old('is_announced') ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-input text-primary focus:ring-ring">
                    <span class="text-sm font-semibold text-foreground">Announce this voucher</span>
                    <span class="text-xs text-muted-foreground">(shows a banner with the code across the entire site)</span>
                </label>
            </div>

        </div>

        <div class="flex items-center gap-4 pt-8 mt-8 border-t border-border">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 text-sm font-bold transition rounded-xl bg-primary text-primary-foreground hover:bg-primary/90 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Create Voucher
            </button>
            <a href="{{ route('admin.vouchers.index') }}"
               class="text-sm font-semibold text-muted-foreground transition-colors hover:text-foreground">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
