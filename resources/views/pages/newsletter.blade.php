@extends('layouts.app')

@section('content')
<div class="container px-4 py-10 mx-auto">
    <div class="mb-6 text-sm text-muted-foreground">
        <a href="/" class="transition hover:text-gray-700">Home</a>
        <span class="mx-2">></span>
        <span class="text-foreground">Newsletter</span>
    </div>
</div>

<x-newsletter-signup />
@endsection
