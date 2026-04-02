@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-200px)] py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md p-8 space-y-8 bg-white border border-gray-100 shadow-2xl rounded-3xl md:p-10 ring-1 ring-gray-900/5">
        
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 mb-6 rounded-full bg-cyan-50 text-cyan-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
            </div>
            
            <h2 class="text-3xl font-extrabold tracking-tight text-gray-900">
                Reset Password
            </h2>
            <p class="mt-3 text-sm leading-relaxed text-gray-500">
                Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.
            </p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-6">
            @csrf

            <div>
                <label for="email" class="block mb-1.5 text-sm font-bold text-gray-900">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                    </div>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus class="block w-full py-3.5 pl-11 pr-4 text-sm placeholder-gray-400 transition-colors border-gray-200 rounded-xl shadow-sm bg-gray-50 focus:bg-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full px-6 py-3.5 text-sm font-bold text-white transition-all bg-cyan-600 rounded-xl shadow-lg hover:bg-cyan-700 hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 active:scale-95 flex justify-center items-center">
                    <span>Email Reset Link</span>
                    <svg class="w-4 h-4 ml-2 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </button>
            </div>

            <div class="pt-4 text-center">
                <a href="{{ route('login') }}" class="text-sm font-bold text-gray-500 transition-colors hover:text-orange-600">
                    &larr; Back to Login
                </a>
            </div>
        </form>

        <div class="pt-6 mt-8 text-xs text-center text-gray-400 border-t border-gray-100">
            If you're having trouble receiving the email, please check your spam folder or <a href="{{ Route::has('contact') ? route('contact') : '#' }}" class="transition-colors hover:text-cyan-600 hover:underline">contact support</a>.
        </div>
    </div>
</div>
@endsection