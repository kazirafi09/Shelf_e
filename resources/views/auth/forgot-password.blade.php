<x-guest-layout>
    <div class="max-w-md py-2 mx-auto">
        <div class="mb-8 text-center">
            <h2 class="text-4xl font-extrabold tracking-tight text-gray-900">
                Reset Password
            </h2>
            <p class="mt-4 text-sm leading-relaxed text-gray-500">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.') }}
            </p>
        </div>

        <x-auth-session-status class="mb-6" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-8">
                <label for="email" class="block mb-1 text-sm font-bold text-gray-900">Email Address</label>
                <input id="email" type="email" name="email" :value="old('email')" placeholder="Enter your registered email" required autofocus 
                       class="block w-full px-4 py-3 text-sm placeholder-gray-400 border-gray-200 rounded-lg shadow-sm bg-gray-50 focus:border-cyan-500 focus:ring-cyan-500" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex flex-col space-y-4">
                <button type="submit" class="w-full px-6 py-4 text-sm font-bold text-white transition rounded-full shadow-md bg-cyan-500 hover:bg-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">
                    {{ __('Email Password Reset Link') }}
                </button>

                <a href="{{ route('login') }}" class="text-sm font-bold text-center text-gray-400 transition hover:text-cyan-500">
                    Back to Login
                </a>
            </div>
        </form>

        <div class="mt-12 text-[11px] text-gray-400 text-center leading-relaxed">
            If you're having trouble receiving the email, please check your spam folder or <a href="#" class="text-cyan-500 hover:underline">contact support</a>.
        </div>
    </div>
</x-guest-layout>