@props([
    'href' => '#',       // The URL for the link
    'color' => 'primary', // primary, secondary, accent, danger, gray
    'size' => 'md',      // sm, md, lg
    'icon' => null,      // optional FontAwesome icon
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition duration-300';
    $sizes = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-5 py-2.5 text-base',
    ];

    $colors = [
        'primary' => 'bg-primary-500 hover:bg-primary-600 text-white',
        'secondary' => 'bg-secondary-500 hover:bg-secondary-600 text-white',
        'accent' => 'bg-accent-500 hover:bg-accent-600 text-white',
        'danger' => 'bg-red-500 hover:bg-red-600 text-white',
        'gray' => 'bg-gray-100 hover:bg-gray-200 text-primary-700',
    ];
@endphp

<a href="{{ $href }}"
   {{ $attributes->merge(['class' => "$baseClasses {$sizes[$size]} {$colors[$color]}"]) }}>
    @if ($icon)
        <i class="{{ $icon }} mr-2"></i>
    @endif
    {{ $slot }}
</a>
