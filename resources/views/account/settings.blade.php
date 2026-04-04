@extends('layouts.dashboard')

@section('page-title', 'Account Settings')

@section('dashboard-content')

@if(session('status') === 'profile-updated')
    <div class="p-4 text-sm font-medium text-green-700 border border-green-200 rounded-xl bg-green-50">
        Profile updated successfully.
    </div>
@endif

@if(session('status') === 'password-updated')
    <div class="p-4 text-sm font-medium text-green-700 border border-green-200 rounded-xl bg-green-50">
        Password updated successfully.
    </div>
@endif

{{-- Profile Information --}}
<div class="overflow-hidden bg-card text-card-foreground border border-border shadow-sm rounded-2xl">
    <div class="px-6 py-5 border-b border-border">
        <h2 class="text-base font-bold text-foreground">Profile Information</h2>
        <p class="mt-1 text-sm text-muted-foreground">Update your name and email address.</p>
    </div>
    <form action="{{ route('profile.update') }}" method="POST" class="px-6 py-5 space-y-4">
        @csrf
        @method('PATCH')

        <div>
            <label class="block mb-1 text-sm font-medium text-foreground">Full Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                   class="w-full px-4 py-2.5 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg
                          @error('name') border-red-400 @enderror">
            @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block mb-1 text-sm font-medium text-foreground">Email Address</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                   class="w-full px-4 py-2.5 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg
                          @error('email') border-red-400 @enderror">
            @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror

            @if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <p class="mt-2 text-sm text-amber-600">
                    Your email address is unverified.
                    <button form="send-verification" class="underline font-medium hover:text-amber-800">
                        Resend verification email.
                    </button>
                </p>
                <form id="send-verification" method="POST" action="{{ route('verification.send') }}">@csrf</form>
            @endif
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="px-5 py-2.5 text-sm font-bold text-white bg-cyan-600 rounded-xl hover:bg-cyan-700 transition-colors">
                Save Changes
            </button>
        </div>
    </form>
</div>

{{-- Change Password --}}
<div class="overflow-hidden bg-card text-card-foreground border border-border shadow-sm rounded-2xl">
    <div class="px-6 py-5 border-b border-border">
        <h2 class="text-base font-bold text-foreground">Change Password</h2>
        <p class="mt-1 text-sm text-muted-foreground">Use a strong password of at least 8 characters.</p>
    </div>
    <form action="{{ route('password.update') }}" method="POST" class="px-6 py-5 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block mb-1 text-sm font-medium text-foreground">Current Password</label>
            <input type="password" name="current_password" autocomplete="current-password" required
                   class="w-full px-4 py-2.5 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg
                          @error('current_password', 'updatePassword') border-red-400 @enderror">
            @error('current_password', 'updatePassword')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block mb-1 text-sm font-medium text-foreground">New Password</label>
            <input type="password" name="password" autocomplete="new-password" required
                   class="w-full px-4 py-2.5 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg
                          @error('password', 'updatePassword') border-red-400 @enderror">
            @error('password', 'updatePassword')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block mb-1 text-sm font-medium text-foreground">Confirm New Password</label>
            <input type="password" name="password_confirmation" autocomplete="new-password" required
                   class="w-full px-4 py-2.5 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg">
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    class="px-5 py-2.5 text-sm font-bold text-white bg-cyan-600 rounded-xl hover:bg-cyan-700 transition-colors">
                Update Password
            </button>
        </div>
    </form>
</div>

{{-- Danger Zone --}}
<div class="overflow-hidden bg-white border border-red-100 shadow-sm rounded-2xl" x-data="{ confirmDelete: false }">
    <div class="px-6 py-5 border-b border-red-100">
        <h2 class="text-base font-bold text-red-700">Delete Account</h2>
        <p class="mt-1 text-sm text-muted-foreground">Permanently delete your account and all associated data.</p>
    </div>
    <div class="px-6 py-5">
        <button @click="confirmDelete = true"
                class="px-5 py-2.5 text-sm font-bold text-red-600 border border-red-300 rounded-xl hover:bg-red-50 transition-colors">
            Delete My Account
        </button>

        <div x-show="confirmDelete" x-transition
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40"
             style="display:none">
            <div class="w-full max-w-md bg-card text-card-foreground rounded-2xl shadow-xl p-6" @click.outside="confirmDelete = false">
                <h3 class="text-lg font-bold text-foreground mb-2">Are you sure?</h3>
                <p class="text-sm text-muted-foreground mb-5">This will permanently delete your account. This action cannot be undone.</p>

                <form action="{{ route('profile.destroy') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('DELETE')
                    <div>
                        <label class="block mb-1 text-sm font-medium text-foreground">Confirm your password</label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-2.5 bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-lg
                                      @error('password', 'userDeletion') border-red-400 @enderror">
                        @error('password', 'userDeletion')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="confirmDelete = false"
                                class="px-4 py-2 text-sm font-semibold text-foreground bg-background border border-border rounded-lg hover:bg-muted transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-5 py-2 text-sm font-bold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                            Yes, Delete Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
