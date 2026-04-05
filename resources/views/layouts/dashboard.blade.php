@extends('layouts.app')

@section('content')
<div class="container px-4 py-8 mx-auto max-w-7xl" x-data="{ mounted: false }" x-init="setTimeout(() => mounted = true, 50)">

    <div class="mb-6 transition-all duration-700 ease-out transform md:mb-8"
         :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-4'">
        <h1 class="text-2xl font-extrabold tracking-tight text-foreground md:text-3xl">@yield('page-title', 'My Dashboard')</h1>
        <p class="mt-1 text-sm font-medium text-muted-foreground">Welcome back, <span class="text-gray-900">{{ auth()->user()->name }}</span>!</p>
    </div>

    <div class="flex flex-col gap-6 md:flex-row md:gap-8">

        {{-- Sidebar --}}
        <aside class="w-full transition-all duration-700 ease-out delay-100 transform md:w-64 shrink-0"
               :class="mounted ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4'">
            <nav class="flex flex-wrap gap-2 pb-4 md:flex-col md:space-y-1 md:gap-0 md:pb-0">
                @php
                    $current = Route::currentRouteName();
                    $navItems = [
                        [
                            'route' => 'dashboard',
                            'label' => 'Orders & Stats',
                            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
                        ],
                        [
                            'route' => 'wishlist.index',
                            'label' => 'My Wishlist',
                            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>',
                        ],
                        [
                            'route' => 'wallet.index',
                            'label' => 'My Wallet',
                            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        ],
                        [
                            'route' => 'addresses.index',
                            'label' => 'Saved Addresses',
                            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>',
                        ],
                        [
                            'route' => 'account.settings',
                            'label' => 'Account Settings',
                            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
                        ],
                    ];
                @endphp

                @foreach($navItems as $item)
                    @php $active = $current === $item['route']; @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center px-4 py-2.5 text-sm transition-all duration-300 rounded-xl md:py-3 md:text-base
                              {{ $active
                                  ? 'font-bold text-sidebar-primary bg-sidebar-accent border border-sidebar-border shadow-sm'
                                  : 'font-medium text-sidebar-foreground/70 border border-sidebar-border shadow-sm bg-sidebar md:border-transparent md:shadow-none md:bg-transparent hover:text-sidebar-accent-foreground hover:bg-sidebar-accent' }}">
                        <svg class="w-5 h-5 mr-2 md:mr-3 shrink-0 {{ $active ? 'text-sidebar-primary' : 'text-sidebar-foreground/40' }}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $item['svg'] !!}
                        </svg>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 min-w-0 space-y-6 transition-all duration-700 ease-out delay-200 transform"
             :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
            @yield('dashboard-content')
        </div>
    </div>
</div>
@endsection
