@php
    $settings = \App\Models\AdminSetting::select(
        'facebook_page',
        'facebook_group',
        'whatsapp_channel',
        'telegram_channel',
        'linkedin',
        'twitter_x',
        'youtube',
        'tiktok',
    )->first();

    // Map all links with label + Font Awesome classes
    $socialLinks = [
        [
            'url' => $settings?->facebook_page,
            'label' => 'Facebook',
            'icon' => 'fab fa-facebook-f',
        ],
        [
            'url' => $settings?->facebook_group,
            'label' => 'Facebook Group',
            'icon' => 'fab fa-facebook',
        ],
        [
            'url' => $settings?->whatsapp_channel,
            'label' => 'WhatsApp',
            'icon' => 'fab fa-whatsapp',
        ],
        [
            'url' => $settings?->telegram_channel,
            'label' => 'Telegram',
            'icon' => 'fab fa-telegram-plane',
        ],
        [
            'url' => $settings?->linkedin,
            'label' => 'LinkedIn',
            'icon' => 'fab fa-linkedin-in',
        ],
        [
            'url' => $settings?->twitter_x,
            'label' => 'Twitter / X',
            'icon' => 'fab fa-x-twitter',
        ],
        [
            'url' => $settings?->youtube,
            'label' => 'YouTube',
            'icon' => 'fab fa-youtube',
        ],
        [
            'url' => $settings?->tiktok,
            'label' => 'TikTok',
            'icon' => 'fab fa-tiktok',
        ],
    ];
@endphp

<div class="flex space-x-4 mt-4">
    @foreach ($socialLinks as $item)
        @if ($item['url'])
            <a href="{{ $item['url'] }}" target="_blank"
                class="w-10 h-10 bg-accent-500 rounded-full flex items-center justify-center hover:bg-accent-600 transition duration-300"
                aria-label="{{ $item['label'] }}">
                <i class="{{ $item['icon'] }} text-white"></i>
            </a>
        @endif
    @endforeach
</div>
