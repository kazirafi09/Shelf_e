@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-200px)] py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md p-8 space-y-8 bg-white border border-gray-100 shadow-2xl rounded-3xl md:p-10 ring-1 ring-gray-900/5">
        
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 mb-6 rounded-full bg-cyan-50 text-cyan-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            
            <h2 class="text-3xl font-extrabold tracking-tight text-gray-900">
                Verify Your Email
            </h2>
            <p class="mt-3 text-sm leading-relaxed text-gray-500">
                Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="p-4 text-sm font-medium text-green-700 border border-green-200 bg-green-50 rounded-xl">
                A new verification link has been sent to the email address you provided during registration.
            </div>
        @endif

        <div class="flex flex-col items-center justify-between mt-8 space-y-5">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full px-6 py-3.5 text-sm font-bold text-white transition-all bg-cyan-600 rounded-xl shadow-lg hover:bg-cyan-700 hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 active:scale-95 flex justify-center items-center">
                    <span>Resend Verification Email</span>
                    <svg class="w-4 h-4 ml-2 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm font-bold text-gray-400 transition-colors hover:text-red-500 hover:underline underline-offset-4">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</div>
@endsection