@props(['url', 'label'])

<a href="{{ $url }}"
    class="block px-3 py-2 text-sm {{ request()->fullUrlIs($url) ? 'text-accent-600' : 'text-primary-600' }} hover:text-accent-600 rounded ">{{ $label }}</a>
