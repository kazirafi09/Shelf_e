@extends('layouts.admin')

@section('title', 'Coin Balances')
@section('subtitle', 'View and manually adjust user coin balances.')

@section('admin-content')

@if(session('success'))
    <div class="p-4 mb-6 text-sm font-bold text-green-700 bg-green-100 border border-green-200 rounded-xl">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="p-4 mb-6 text-sm text-red-700 border border-red-200 rounded-xl bg-red-50">
        <ul class="pl-5 space-y-1 list-disc">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="overflow-hidden bg-white shadow-sm rounded-2xl ring-1 ring-gray-900/5">
    <table class="w-full text-left">
        <thead class="bg-gray-50/50">
            <tr>
                <th class="px-6 py-4 text-xs font-bold tracking-wider text-gray-400 uppercase">User</th>
                <th class="px-6 py-4 text-xs font-bold tracking-wider text-gray-400 uppercase">Email</th>
                <th class="px-6 py-4 text-xs font-bold tracking-wider text-gray-400 uppercase">Coin Balance</th>
                <th class="px-6 py-4 text-xs font-bold tracking-wider text-gray-400 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($users as $user)
            <tr x-data="{ showAdjustForm: false }" class="group">
                <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $user->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-sm font-bold text-amber-700 bg-amber-50 border border-amber-200 rounded-full">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                        </svg>
                        {{ number_format($user->coin_balance) }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <button
                        @click="showAdjustForm = !showAdjustForm"
                        :class="showAdjustForm ? 'bg-gray-100 text-gray-700' : 'bg-cyan-50 text-cyan-700 hover:bg-cyan-100'"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold border border-transparent rounded-lg transition-colors"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span x-text="showAdjustForm ? 'Cancel' : 'Adjust'"></span>
                    </button>
                </td>
            </tr>

            {{-- Inline Adjust Form Row --}}
            <tr x-show="showAdjustForm"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="bg-cyan-50/40"
                style="display: none;">
                <td colspan="4" class="px-6 py-4">
                    <form action="{{ route('admin.coins.adjust', $user) }}" method="POST"
                          class="flex flex-wrap items-end gap-3">
                        @csrf

                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Type</label>
                            <select name="type"
                                    class="py-2 text-sm border-gray-200 rounded-lg shadow-sm focus:ring-cyan-500 focus:border-cyan-500">
                                <option value="credit">Credit</option>
                                <option value="debit">Debit</option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Amount</label>
                            <input type="number" name="amount" min="1" placeholder="e.g. 100"
                                   class="py-2 text-sm border-gray-200 rounded-lg shadow-sm focus:ring-cyan-500 focus:border-cyan-500 w-32">
                        </div>

                        <div class="flex flex-col flex-1 gap-1 min-w-48">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Description</label>
                            <input type="text" name="description" placeholder="e.g. Loyalty reward"
                                   class="py-2 text-sm border-gray-200 rounded-lg shadow-sm focus:ring-cyan-500 focus:border-cyan-500">
                        </div>

                        <button type="submit"
                                class="px-5 py-2 text-sm font-bold text-white transition bg-cyan-600 rounded-lg hover:bg-cyan-700 active:scale-95 shrink-0">
                            Confirm
                        </button>
                    </form>
                </td>
            </tr>

            @empty
            <tr>
                <td colspan="4" class="px-6 py-16 text-sm font-medium text-center text-gray-400">
                    No users found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-8">
    {{ $users->links() }}
</div>

@endsection
