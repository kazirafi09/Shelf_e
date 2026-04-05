@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-200px)] py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md p-8 space-y-8 bg-white border border-gray-100 shadow-2xl rounded-3xl md:p-10 ring-1 ring-gray-900/5">
        
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 mb-6 text-gray-700 rounded-full bg-gray-50">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h2 class="text-3xl font-extrabold tracking-tight text-gray-900">
                Secure Area
            </h2>
            <p class="mt-3 text-sm leading-relaxed text-gray-500">
                This is a secure area of the application. Please confirm your password before continuing.
            </p>
        </div>

        <form method="POST" action="{{ route('password.confirm') }}" class="mt-2 space-y-6">
            @csrf

            <div>
                <label for="password" class="block mb-1.5 text-sm font-bold text-gray-900">Password</label>
                <div class="relative" x-data="{ show: false }">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input id="password" :type="show ? 'text' : 'password'" name="password" placeholder="••••••••" required autocomplete="current-password" class="block w-full py-3.5 pl-11 pr-12 text-sm placeholder-gray-400 transition-colors border-gray-200 rounded-xl shadow-sm bg-gray-50 focus:bg-white focus:border-gray-500 focus:ring-2 focus:ring-gray-500/20" />
                    
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <button type="button" @click="show = !show" class="p-1 text-gray-400 transition-colors rounded-lg focus:outline-none hover:text-gray-700 focus:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path x-show="!show" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path x-show="!show" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                <path x-show="show" style="display: none;" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full px-6 py-3.5 text-sm font-bold text-white transition-all bg-gray-900 rounded-xl shadow-lg hover:bg-black hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 active:scale-95 flex justify-center items-center">
                    <span>Confirm Password</span>
                    <svg class="w-4 h-4 ml-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection