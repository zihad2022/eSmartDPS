@php
    $settings = \App\Models\AdminSetting::select('site_logo', 'site_name')->first();
    $logo = $settings?->site_logo_url;
@endphp

<div>
    @if ($logo)
        <img src="{{ asset($logo) }}" alt="{{ $settings?->site_name ?? config('app.name') }}" class="w-14 h-14">
    @else
        <span class="text-xl font-bold">{{ $settings?->site_name ?? config('app.name', 'Laravel') }}</span>
    @endif
</div>
