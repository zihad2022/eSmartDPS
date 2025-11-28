@php
    $settings = \App\Models\AdminSetting::select('facebook_page_url','facebook_group_url','whatsapp_channel_url','telegram_channel_url','linkedin_url','twitter_x_url','youtube_url','tiktok_url')->first();
@endphp

<div class="flex space-x-4 mt-4">
    @if ($settings?->facebook_page_url)
        <a href="{{ $settings->facebook_page_url }}" class="text-gray-300 hover:text-white">Facebook</a>
    @endif

    @if ($settings?->facebook_group_url)
        <a href="{{ $settings->facebook_group_url }}" class="text-gray-300 hover:text-white">Facebook Group</a>
    @endif

    @if ($settings?->whatsapp_channel_url)
        <a href="{{ $settings->whatsapp_channel_url }}" class="text-gray-300 hover:text-white">WhatsApp</a>
    @endif

    @if ($settings?->telegram_channel_url)
        <a href="{{ $settings->telegram_channel_url }}" class="text-gray-300 hover:text-white">Telegram</a>
    @endif

    @if ($settings?->linkedin_url)
        <a href="{{ $settings->linkedin_url }}" class="text-gray-300 hover:text-white">LinkedIn</a>
    @endif

    @if ($settings?->twitter_x_url)
        <a href="{{ $settings->twitter_x_url }}" class="text-gray-300 hover:text-white">Twitter</a>
    @endif

    @if ($settings?->youtube_url)
        <a href="{{ $settings->youtube_url }}" class="text-gray-300 hover:text-white">YouTube</a>
    @endif

    @if ($settings?->tiktok_url)
        <a href="{{ $settings->tiktok_url }}" class="text-gray-300 hover:text-white">TikTok</a>
    @endif
</div>
