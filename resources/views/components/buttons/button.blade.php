@props([
    'href' => null,     // if provided → render <a>
    'type' => 'submit', // button type: button|submit|reset
    'variant' => 'primary', // primary|secondary|danger|gray
    'icon' => null,     // optional FontAwesome icon class
])

@php
    // Tailwind preset styles
    $baseClasses = "px-4 py-2 rounded-lg text-sm font-medium transition duration-300 inline-flex items-center gap-2";

    $variants = [
        'primary'   => "bg-accent-500 hover:bg-accent-600 text-white",
        'secondary' => "bg-blue-100 hover:bg-blue-200 text-blue-700",
        'danger'    => "bg-red-100 hover:bg-red-200 text-red-700",
        'gray'      => "bg-gray-100 hover:bg-gray-200 text-gray-700",
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif
        {{ $slot }}
    </button>
@endif
