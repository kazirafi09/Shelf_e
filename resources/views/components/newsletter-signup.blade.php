{{--
    Newsletter Signup Component
    ──────────────────────────────────────────────────────────────────
    Four mutually exclusive states, resolved in PHP before Alpine loads:

      guest    – visitor is not logged in → show login prompt
      form     – logged in, not yet subscribed → show subscribe button
      already  – logged in, already subscribed → show info card
      success  – just subscribed (AJAX response or session flash)
                 → show discount code card

    The authenticated user's email is never taken from the request body;
    it is always read from auth()->user()->email on the server.
    ──────────────────────────────────────────────────────────────────
--}}
@php
    use App\Models\Subscriber;

    $isGuest    = auth()->guest();
    $userEmail  = $isGuest ? '' : auth()->user()->email;
    $isAlready  = !$isGuest && Subscriber::where('email', $userEmail)->exists();

    // Session flash is set by the non-AJAX fallback path
    $flashSuccess = session('newsletter_success', false);
    $flashAlready = session('newsletter_already_subscribed', false);
    $flashCode    = session('newsletter_discount', 'FIRST15');

    // Derive the initial Alpine state
    if ($flashSuccess) {
        $initialState = 'success';
    } elseif ($flashAlready || $isAlready) {
        $initialState = 'already';
    } elseif ($isGuest) {
        $initialState = 'guest';
    } else {
        $initialState = 'form';
    }
@endphp

<section
    class="py-20 bg-white border-t border-gray-100"
    x-data="{
        state:        '{{ $initialState }}',
        discountCode: '{{ $flashCode }}',
        loading:      false,
        error:        '',
        copied:       false,

        async submit() {
            this.error   = '';
            this.loading = true;

            try {
                const res = await fetch('{{ route('newsletter.subscribe') }}', {
                    method:  'POST',
                    headers: {
                        'Accept':       'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                });

                const data = await res.json();

                if (data.already_subscribed) {
                    this.state = 'already';
                } else if (res.ok) {
                    this.discountCode = data.discount_code;
                    this.state        = 'success';
                } else {
                    this.error = data.message ?? 'Something went wrong. Please try again.';
                }
            } catch {
                this.error = 'Network error. Please check your connection and try again.';
            } finally {
                this.loading = false;
            }
        },

        copy() {
            navigator.clipboard.writeText(this.discountCode).then(() => {
                this.copied = true;
                setTimeout(() => { this.copied = false; }, 2000);
            });
        },
    }"
>
    <div class="max-w-2xl px-4 mx-auto text-center">

        {{-- Icon --}}
        <div class="inline-flex items-center justify-center w-14 h-14 mb-6 bg-gray-50 border border-gray-200 rounded-2xl shadow-sm">
            <svg class="w-7 h-7 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>

        <h2 class="mb-2 text-3xl font-extrabold tracking-tight text-gray-900">
            Get your first discount!
        </h2>
        <p class="mb-10 text-base text-gray-500">
            Subscribe to our newsletter and get a
            <span class="font-semibold text-gray-700">15% discount code</span>
            for your first order.
        </p>

        {{-- ══════════════════════════════════════════════════════════ --}}
        {{-- STATE: guest                                               --}}
        {{-- ══════════════════════════════════════════════════════════ --}}
        <div
            x-show="state === 'guest'"
            x-cloak
            class="max-w-md mx-auto p-8 bg-gray-50 border border-gray-200 rounded-2xl"
        >
            <p class="mb-1 text-base font-semibold text-gray-900">Sign in to subscribe</p>
            <p class="mb-6 text-sm text-gray-500">
                You need to be logged in so we can link the discount to your account.
            </p>
            <a
                href="{{ route('login') }}"
                class="inline-flex items-center gap-2 px-6 py-3 text-sm font-bold text-white bg-gray-800 rounded-xl hover:bg-gray-900 transition-colors active:scale-95"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Log in to subscribe
            </a>
        </div>

        {{-- ══════════════════════════════════════════════════════════ --}}
        {{-- STATE: form (logged in, not yet subscribed)               --}}
        {{-- ══════════════════════════════════════════════════════════ --}}
        <form
            x-show="state === 'form'"
            x-cloak
            action="{{ route('newsletter.subscribe') }}"
            method="POST"
            @submit.prevent="submit()"
            class="max-w-xl mx-auto"
        >
            @csrf

            {{--
                Read-only email display + subscribe button, flex row.
                The email is never editable — it is always the authenticated
                user's account email, confirmed server-side.
            --}}
            <div class="flex items-stretch border border-gray-300 rounded-xl shadow-sm overflow-hidden focus-within:ring-2 focus-within:ring-gray-400 focus-within:border-transparent transition-shadow">
                <span class="flex-1 py-4 pl-5 pr-3 text-sm text-gray-600 bg-gray-50 text-left truncate select-all min-w-0">
                    {{ $userEmail }}
                </span>
                <button
                    type="submit"
                    :disabled="loading"
                    class="inline-flex items-center gap-2 px-6 text-xs font-bold tracking-widest text-white uppercase bg-gray-800 hover:bg-gray-900 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shrink-0 active:scale-95"
                >
                    <svg
                        x-show="loading"
                        x-cloak
                        class="w-3.5 h-3.5 animate-spin"
                        fill="none" viewBox="0 0 24 24"
                    >
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="loading ? 'Sending…' : 'Subscribe'"></span>
                </button>
            </div>

            {{-- Network / unexpected error --}}
            <p
                x-show="error"
                x-cloak
                x-text="error"
                class="mt-3 text-sm font-medium text-red-500"
            ></p>

            <p class="mt-4 text-xs text-gray-400">No spam, ever. Unsubscribe at any time.</p>
        </form>

        {{-- ══════════════════════════════════════════════════════════ --}}
        {{-- STATE: already subscribed                                  --}}
        {{-- ══════════════════════════════════════════════════════════ --}}
        <div
            x-show="state === 'already'"
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="max-w-md mx-auto p-7 bg-gray-50 border border-gray-200 rounded-2xl"
        >
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-white border border-gray-200 rounded-full shadow-sm">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-base font-bold text-gray-900">You're already subscribed!</p>
            <p class="mt-1.5 text-sm text-gray-500">
                <span class="font-medium text-gray-700">{{ $userEmail }}</span>
                is already on our newsletter list.
            </p>
            <p class="mt-3 text-xs text-gray-400">
                If you haven't used your <span class="font-semibold">FIRST15</span> discount yet, apply it at checkout.
            </p>
        </div>

        {{-- ══════════════════════════════════════════════════════════ --}}
        {{-- STATE: success (just subscribed)                          --}}
        {{-- ══════════════════════════════════════════════════════════ --}}
        <div
            x-show="state === 'success'"
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="max-w-md mx-auto"
        >
            <div class="flex items-center justify-center gap-2 mb-6 text-sm font-semibold text-green-700">
                <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          clip-rule="evenodd"/>
                </svg>
                You're subscribed! Here is your exclusive code:
            </div>

            <div class="p-6 bg-gray-50 border-2 border-dashed border-gray-300 rounded-2xl">
                <p class="mb-4 text-xs font-semibold tracking-widest text-gray-400 uppercase">
                    Your discount code
                </p>
                <div class="flex items-center justify-between gap-4">
                    <span
                        x-text="discountCode"
                        class="text-3xl font-black tracking-[0.2em] text-gray-900 select-all"
                    ></span>
                    <button
                        type="button"
                        @click="copy()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold tracking-wide text-gray-700 uppercase bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors active:scale-95 shrink-0"
                    >
                        <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <svg x-show="copied" class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                    </button>
                </div>
                <p class="mt-4 text-xs text-gray-500">
                    Apply this code at checkout to receive
                    <span class="font-semibold text-gray-700">15% off</span>
                    your first order. Valid for one use only.
                </p>
            </div>
        </div>

    </div>
</section>
