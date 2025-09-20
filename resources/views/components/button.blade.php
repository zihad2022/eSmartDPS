@props([
    'type' => 'button',
    'color' => 'primary',  {{-- primary, secondary, accent, danger --}}
    'size' => 'md',       {{-- sm, md, lg --}}
    'disabled' => false,
])

@php
    $baseClasses = "inline-flex items-center justify-center font-medium rounded-lg transition focus:outline-none focus:ring-2 focus:ring-offset-2";

    $colors = [
        'primary' => 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500',
        'secondary' => 'bg-gray-200 text-gray-800 hover:bg-gray-300 focus:ring-gray-400',
        'accent' => 'bg-accent-500 text-white hover:bg-accent-600 focus:ring-accent-400',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    ];

    $sizes = [
        'sm' => 'px-3 py-1 text-sm',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];
@endphp

<button
    type="{{ $type }}"
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge([
        'class' => "$baseClasses {$colors[$color]} {$sizes[$size]} " . ($disabled ? 'opacity-50 cursor-not-allowed' : ''),
    ]) }}
>
    {{ $slot }}
</button>
