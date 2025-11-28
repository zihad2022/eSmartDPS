@php
    $settings = \App\Models\AdminSetting::select('site_description')->first();
@endphp

<p class="text-gray-300 leading-relaxed">
    {{ $settings?->site_description ?? '' }}
</p>
