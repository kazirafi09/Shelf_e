@extends('layouts.app')

@section('content')
<div class="container px-4 py-12 mx-auto max-w-3xl">
    <h1 class="mb-6 text-4xl font-extrabold text-foreground">Frequently Asked Questions</h1>

    @if($content)
        <div class="prose prose-gray max-w-none text-foreground whitespace-pre-line">
            {{ $content }}
        </div>
    @else
        <p class="text-muted-foreground">No FAQs have been added yet. Please <a href="{{ route('contact') }}" class="underline hover:text-foreground">contact us</a> if you have questions.</p>
    @endif
</div>
@endsection
