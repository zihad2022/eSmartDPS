@php
    $settings = \App\Models\AdminSetting::select('site_logo', 'site_name')->first();
@endphp

<a href="{{ route('home') }}">
    <span class="font-display font-bold text-xl text-primary-900">
        @if (!empty($settings?->site_logo_url))
            <img src="{{ asset($settings->site_logo_url) }}" alt="{{ $settings?->site_name ?? config('app.name') }}" class="w-12 h-12">
        @else
            {{ $settings?->site_name ?? config('app.name', 'Laravel') }}
        @endif
    </span>
</a>
