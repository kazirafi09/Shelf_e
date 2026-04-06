@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
    {{-- Replace the src URL with your actual Shelf-e logo --}}
    <img src="{{ asset('images/shelfe-logo.png') }}" class="logo" alt="Shelf-e Logo">
</a>
</td>
</tr>