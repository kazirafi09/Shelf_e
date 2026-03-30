@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-200px)] py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md p-8 space-y-8 bg-white border border-gray-100 shadow-2xl rounded-3xl md:p-10 ring-1 ring-gray-900/5">
        
        <div class="text-center">
            <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 md:text-4xl">
                Create Account
            </h2>
            <p class="mt-3 text-sm text-gray-500">Join the <span class="font-bold text-cyan-600">Shelf-E</span> community today</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
            @csrf

            <div>
                <label for="name" class="block mb-1.5 text-sm font-bold text-gray-900">Full Name</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <input id="name" type="text" name="name" :value="old('name')" placeholder="John Doe" required autofocus autocomplete="name" class="block w-full py-3.5 pl-11 pr-4 text-sm placeholder-gray-400 transition-colors border-gray-200 rounded-xl shadow-sm bg-gray-50 focus:bg-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20" />
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <label for="email" class="block mb-1.5 text-sm font-bold text-gray-900">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                    </div>
                    <input id="email" type="email" name="email" :value="old('email')" placeholder="you@example.com" required autocomplete="username" class="block w-full py-3.5 pl-11 pr-4 text-sm placeholder-gray-400 transition-colors border-gray-200 rounded-xl shadow-sm bg-gray-50 focus:bg-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <label for="password" class="block mb-1.5 text-sm font-bold text-gray-900">Password</label>
                <div class="relative" x-data="{ show: false }">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input id="password" :type="show ? 'text' : 'password'" name="password" placeholder="Min. 8 characters" required autocomplete="new-password" class="block w-full py-3.5 pl-11 pr-12 text-sm placeholder-gray-400 transition-colors border-gray-200 rounded-xl shadow-sm bg-gray-50 focus:bg-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20" />
                    
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <button type="button" @click="show = !show" class="p-1 text-gray-400 transition-colors rounded-lg focus:outline-none hover:text-cyan-600 focus:bg-gray-100">
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

            <div>
                <label for="password_confirmation" class="block mb-1.5 text-sm font-bold text-gray-900">Confirm Password</label>
                <div class="relative" x-data="{ show: false }">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input id="password_confirmation" :type="show ? 'text' : 'password'" name="password_confirmation" placeholder="Repeat Password" required autocomplete="new-password" class="block w-full py-3.5 pl-11 pr-12 text-sm placeholder-gray-400 transition-colors border-gray-200 rounded-xl shadow-sm bg-gray-50 focus:bg-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20" />
                    
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <button type="button" @click="show = !show" class="p-1 text-gray-400 transition-colors rounded-lg focus:outline-none hover:text-cyan-600 focus:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path x-show="!show" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path x-show="!show" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                <path x-show="show" style="display: none;" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full px-6 py-3.5 text-sm font-bold text-white transition-all bg-cyan-600 rounded-xl shadow-lg hover:bg-cyan-700 hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 active:scale-95 flex justify-center items-center">
                    <span>Create Account</span>
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>

            <div class="pt-6 mt-6 border-t border-gray-100">
                <p class="text-sm text-center text-gray-600">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="font-bold text-orange-600 transition-colors hover:text-orange-600 hover:underline">Sign In</a>
                </p>
            </div>
        </form>

        <div class="pt-6 mt-8 text-xs text-center text-gray-400">
            By signing up, you agree to our <br class="sm:hidden">
            <a href="{{ Route::has('terms') ? route('terms') : '#' }}" class="transition-colors hover:text-cyan-600 hover:underline">Terms of Service</a> and 
            <a href="{{ Route::has('privacy') ? route('privacy') : '#' }}" class="transition-colors hover:text-cyan-600 hover:underline">Privacy Policy</a>.
        </div>
    </div>
</div>
@endsection