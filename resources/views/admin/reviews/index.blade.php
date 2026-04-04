@extends('layouts.admin')

@section('title', 'Review Moderation')
@section('subtitle', 'Approve or reject customer product reviews.')

@section('admin-content')

@if(session('success'))
    <div class="p-4 mb-6 text-sm font-bold text-green-700 bg-green-100 border border-green-200 rounded-xl">
        {{ session('success') }}
    </div>
@endif

<div
    x-data="{ activeTab: 'pending' }"
    class="space-y-6"
>
    {{-- Tab Bar --}}
    <div class="flex space-x-1 bg-background border border-border rounded-xl shadow-sm ring-1 ring-gray-900/5 p-1 w-fit">
        <button
            @click="activeTab = 'pending'"
            :class="activeTab === 'pending'
                ? 'bg-amber-500 text-white shadow'
                : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
            class="flex items-center gap-2 px-5 py-2 text-sm font-semibold rounded-lg transition-all duration-200"
        >
            Pending
            <span
                :class="activeTab === 'pending' ? 'bg-amber-400/50' : 'bg-gray-100 text-gray-600'"
                class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full"
            >{{ $pending->count() }}</span>
        </button>
        <button
            @click="activeTab = 'approved'"
            :class="activeTab === 'approved'
                ? 'bg-emerald-500 text-white shadow'
                : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
            class="flex items-center gap-2 px-5 py-2 text-sm font-semibold rounded-lg transition-all duration-200"
        >
            Approved
            <span
                :class="activeTab === 'approved' ? 'bg-emerald-400/50' : 'bg-gray-100 text-gray-600'"
                class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full"
            >{{ $approved->count() }}</span>
        </button>
        <button
            @click="activeTab = 'rejected'"
            :class="activeTab === 'rejected'
                ? 'bg-red-500 text-white shadow'
                : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
            class="flex items-center gap-2 px-5 py-2 text-sm font-semibold rounded-lg transition-all duration-200"
        >
            Rejected
            <span
                :class="activeTab === 'rejected' ? 'bg-red-400/50' : 'bg-gray-100 text-gray-600'"
                class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full"
            >{{ $rejected->count() }}</span>
        </button>
    </div>

    {{-- Pending Tab --}}
    <div x-show="activeTab === 'pending'" x-transition.opacity.duration.200ms>
        @include('admin.reviews._list', ['reviews' => $pending, 'status' => 'pending'])
    </div>

    {{-- Approved Tab --}}
    <div x-show="activeTab === 'approved'" x-transition.opacity.duration.200ms>
        @include('admin.reviews._list', ['reviews' => $approved, 'status' => 'approved'])
    </div>

    {{-- Rejected Tab --}}
    <div x-show="activeTab === 'rejected'" x-transition.opacity.duration.200ms>
        @include('admin.reviews._list', ['reviews' => $rejected, 'status' => 'rejected'])
    </div>
</div>

@endsection
