@props(['active'])

@php
$classes = ($active ?? false)
            ? 'd-inline-flex align-items-center px-1 pt-1 border-bottom-2 border-primary text-sm font-medium text-dark bg-light'
            : 'd-inline-flex align-items-center px-1 pt-1 border-bottom-2 border-transparent text-sm font-medium text-muted hover:text-dark hover:border-light focus:outline-none focus:text-dark focus:border-light transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
