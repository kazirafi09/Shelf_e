<x-guest-layout>
    <div class="max-w-md py-4 mx-auto">
        <div class="mb-8 text-center">
            <h2 class="text-4xl font-extrabold tracking-tight text-gray-900">
                Welcome Back
            </h2>
        </div>

        <div class="mb-6">
            <button type="button" class="flex items-center justify-center w-full px-4 py-3 space-x-3 transition bg-white border border-gray-300 rounded-full shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                <span class="text-sm font-bold text-gray-700">Login with Google</span>
            </button>
        </div>

        <div class="relative mb-8">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-4 text-gray-400 bg-white">or</span>
            </div>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-5">
                <label for="email" class="block mb-1 text-sm font-bold text-gray-900">Email</label>
                <input id="email" type="email" name="email" :value="old('email')" placeholder="Your Email" required autofocus class="block w-full px-4 py-3 text-sm placeholder-gray-400 border-gray-200 rounded-lg shadow-sm bg-gray-50 focus:border-cyan-500 focus:ring-cyan-500" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="relative mb-4">
                <label for="password" class="block mb-1 text-sm font-bold text-gray-900">Password</label>
                <div class="relative">
                    <input id="password" type="password" name="password" placeholder="Your Password" required class="block w-full px-4 py-3 text-sm placeholder-gray-400 border-gray-200 rounded-lg shadow-sm bg-gray-50 focus:border-cyan-500 focus:ring-cyan-500" />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                        <svg class="w-5 h-5 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between mb-8">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="w-4 h-4 border-gray-300 rounded text-cyan-600 focus:ring-cyan-500">
                    <span class="ml-2 text-xs font-medium text-gray-500">Remember me</span>
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs font-bold transition text-cyan-500 hover:text-cyan-600">Forgot Password?</a>
                @endif
            </div>

            <div>
                <button type="submit" class="w-full px-6 py-4 text-sm font-bold text-white transition rounded-full shadow-md bg-cyan-500 hover:bg-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">
                    Log in
                </button>
            </div>

            <div class="mt-8 text-center">
                <p class="text-sm text-gray-500">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="font-bold text-cyan-500 hover:underline">Sign up Now</a>
                </p>
            </div>
        </form>

        <div class="mt-12 text-[11px] text-gray-400 text-center leading-relaxed">
            By clicking "Login with Google or Email" above, you acknowledge that you have read and understood and agree to Shelf-E <a href="#" class="text-cyan-500 hover:underline">Terms & Conditions</a> and <a href="#" class="text-cyan-500 hover:underline">Privacy Policy</a>.
        </div>
    </div>
</x-guest-layout>