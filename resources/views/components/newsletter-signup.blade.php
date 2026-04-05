{{--
    Newsletter Signup Component
    ──────────────────────────────────────────────────────────────────
    Submits via Fetch (JSON).
    On success: hides the form and reveals the discount code card.
    Usage: <x-newsletter-signup />
    ──────────────────────────────────────────────────────────────────
--}}
<section
    class="py-20 bg-white border-t border-gray-100"
    x-data="{
        email:        '',
        loading:      false,
        success:      false,
        discountCode: '',
        error:        '',
        copied:       false,

        async submit() {
            this.error = '';
            this.loading = true;

            try {
                const res = await fetch('{{ route('newsletter.subscribe') }}', {
                    method:  'POST',
                    headers: {
                        'Content-Type':  'application/json',
                        'Accept':        'application/json',
                        'X-CSRF-TOKEN':  document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ email: this.email }),
                });

                const data = await res.json();

                if (res.ok) {
                    this.success      = true;
                    this.discountCode = data.discount_code;
                } else {
                    /* Laravel 422 returns { errors: { email: ['...'] } } */
                    this.error = data.errors?.email?.[0]
                              ?? data.message
                              ?? 'Something went wrong. Please try again.';
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
                setTimeout(() => this.copied = false, 2000);
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
            Subscribe to our newsletter and get a <span class="font-semibold text-gray-700">15% discount code</span> for your first order.
        </p>

        {{-- ── Subscription form ── --}}
        <form
            x-show="!success"
            @submit.prevent="submit()"
            class="max-w-xl mx-auto"
            novalidate
        >
            {{-- Input + button wrapper --}}
            <div class="relative">
                <input
                    type="email"
                    x-model="email"
                    placeholder="Enter your email address"
                    required
                    autocomplete="email"
                    :disabled="loading"
                    class="w-full py-4 pl-5 pr-40 text-sm text-gray-900 placeholder-gray-400 bg-white border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-gray-400 focus:border-gray-400 focus:outline-none transition-colors disabled:opacity-60"
                >
                <button
                    type="submit"
                    :disabled="loading || !email"
                    class="absolute right-2 top-1/2 -translate-y-1/2 inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold tracking-widest text-white uppercase bg-gray-800 rounded-lg hover:bg-gray-900 disabled:opacity-50 disabled:cursor-not-allowed transition-colors active:scale-95"
                >
                    <svg
                        x-show="loading"
                        class="w-3.5 h-3.5 animate-spin"
                        fill="none" viewBox="0 0 24 24"
                    >
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="loading ? 'Sending…' : 'Subscribe'"></span>
                </button>
            </div>

            {{-- Inline error message --}}
            <p
                x-show="error"
                x-text="error"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="mt-3 text-sm font-medium text-red-500"
            ></p>

            <p class="mt-4 text-xs text-gray-400">
                No spam, ever. Unsubscribe at any time.
            </p>
        </form>

        {{-- ── Success state: discount code reveal ── --}}
        <div
            x-show="success"
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="max-w-md mx-auto"
        >
            {{-- Confirmation banner --}}
            <div class="flex items-center justify-center gap-2 mb-6 text-sm font-semibold text-green-700">
                <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          clip-rule="evenodd"/>
                </svg>
                You're subscribed! Here is your exclusive code:
            </div>

            {{-- Discount code card --}}
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
                    Apply this code at checkout to receive <span class="font-semibold text-gray-700">15% off</span> your first order.
                    Valid for one use only.
                </p>
            </div>
        </div>

    </div>
</section>
