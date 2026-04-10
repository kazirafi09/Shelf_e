@extends('layouts.admin')

@section('title', 'Categories')
@section('subtitle', 'Create, rename, or remove book categories.')

@section('admin-content')

@if(session('success'))
    <div class="p-4 mb-6 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-xl">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="p-4 mb-6 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-xl">
        {{ session('error') }}
    </div>
@endif

<div
    x-data="{
        editModal: false,
        deleteModal: false,
        editId: null,
        editName: '',
        deleteId: null,
        deleteName: '',

        openEdit(id, name) {
            this.editId   = id;
            this.editName = name;
            this.editModal = true;
        },
        openDelete(id, name) {
            this.deleteId   = id;
            this.deleteName = name;
            this.deleteModal = true;
        }
    }"
    class="space-y-6"
>

    {{-- Create Form --}}
    <div class="p-6 bg-card text-card-foreground border border-border shadow-sm rounded-2xl">
        <h2 class="mb-4 text-base font-bold text-foreground">Add New Category</h2>
        <form action="{{ route('admin.categories.store') }}" method="POST" class="flex gap-3">
            @csrf
            <input
                type="text"
                name="name"
                placeholder="e.g. Historical Fiction"
                value="{{ old('name') }}"
                required
                class="flex-1 px-4 py-2.5 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-xl shadow-sm"
            >
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold transition rounded-xl bg-primary text-primary-foreground hover:bg-primary/90 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create
            </button>
        </form>
        @error('name')
            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Categories Table --}}
    <div class="overflow-hidden bg-card text-card-foreground border border-border shadow-sm rounded-2xl">
        <div class="px-6 py-4 border-b border-border">
            <p class="text-sm font-semibold text-foreground">{{ $categories->count() }} categories</p>
        </div>

        @if($categories->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <svg class="w-10 h-10 mb-3 text-border" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <p class="text-sm text-muted-foreground">No categories yet. Create one above.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-muted">
                        <tr>
                            <th class="px-6 py-3 text-xs font-bold tracking-wider text-muted-foreground uppercase">Name</th>
                            <th class="px-6 py-3 text-xs font-bold tracking-wider text-muted-foreground uppercase">Slug</th>
                            <th class="px-6 py-3 text-xs font-bold tracking-wider text-muted-foreground uppercase text-right">Books</th>
                            <th class="px-6 py-3 text-xs font-bold tracking-wider text-muted-foreground uppercase text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($categories as $category)
                            <tr class="hover:bg-muted/50 transition">
                                <td class="px-6 py-4 font-medium text-foreground">{{ $category->name }}</td>
                                <td class="px-6 py-4 text-muted-foreground font-mono text-xs">{{ $category->slug }}</td>
                                <td class="px-6 py-4 text-right text-muted-foreground">{{ $category->products_count }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            type="button"
                                            @click="openEdit({{ $category->id }}, '{{ addslashes($category->name) }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-foreground bg-muted border border-border rounded-lg hover:bg-muted/80 transition"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </button>
                                        <button
                                            type="button"
                                            @click="openDelete({{ $category->id }}, '{{ addslashes($category->name) }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Edit Modal --}}
    <div
        x-show="editModal"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
        style="display: none;"
        @keydown.escape.window="editModal = false"
    >
        <div @click.outside="editModal = false" class="w-full max-w-md p-6 bg-card border border-border rounded-2xl shadow-xl">
            <h3 class="mb-4 text-base font-bold text-foreground">Rename Category</h3>
            <form :action="`{{ url('admin/categories') }}/` + editId" method="POST">
                @csrf
                @method('PUT')
                <input type="text" name="name" x-model="editName" required
                       class="block w-full px-4 py-2.5 text-sm bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-xl shadow-sm mb-4">
                <div class="flex justify-end gap-3">
                    <button type="button" @click="editModal = false"
                            class="px-4 py-2 text-sm font-medium text-muted-foreground hover:text-foreground transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2 text-sm font-bold rounded-xl bg-primary text-primary-foreground hover:bg-primary/90 transition active:scale-95">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div
        x-show="deleteModal"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
        style="display: none;"
        @keydown.escape.window="deleteModal = false"
    >
        <div @click.outside="deleteModal = false" class="w-full max-w-md p-6 bg-card border border-border rounded-2xl shadow-xl">
            <h3 class="mb-2 text-base font-bold text-foreground">Delete Category</h3>
            <p class="mb-5 text-sm text-muted-foreground">
                Are you sure you want to delete <span class="font-semibold text-foreground" x-text="'&quot;' + deleteName + '&quot;'"></span>?
                This cannot be undone.
            </p>
            <form :action="`{{ url('admin/categories') }}/` + deleteId" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-3">
                    <button type="button" @click="deleteModal = false"
                            class="px-4 py-2 text-sm font-medium text-muted-foreground hover:text-foreground transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2 text-sm font-bold rounded-xl bg-red-600 text-white hover:bg-red-700 transition active:scale-95">
                        Delete
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection
