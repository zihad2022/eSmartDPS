{{-- 
    Settings Page Layout
    --------------------
    This Blade file handles the layout for all admin settings pages.
    It contains:
      - Dynamic Settings Navigation (left column)
      - Page Content Slot (right column)
    Pages & their icons are defined in a single $pages array for easy maintenance.

    Author: Zihadur Islam
    Created On: 2025-08-09
    Last Updated: 2025-08-09
--}}

<x-admin.layout.app>

    @php
        /*
        |--------------------------------------------------------------------------
        | SETTINGS PAGES DEFINITION
        |--------------------------------------------------------------------------
        | Key   = Route name
        | label = Display name in navigation
        | icon  = FontAwesome class for icon
        */
        $pages = [
            'admin.settings.general.edit' => ['label' => 'General Settings', 'icon' => 'fas fa-cog'],
            'admin.settings.payments.edit' => ['label' => 'Payment Settings', 'icon' => 'fas fa-money-bill-wave'],
            'admin.settings.contact_info.edit' => ['label' => 'Contact Info Settings', 'icon' => 'fas fa-address-book'],
            'admin.settings.social_media.edit' => ['label' => 'Social Media Settings', 'icon' => 'fas fa-share-alt'],
            'admin.settings.sms.edit' => ['label' => 'SMS Settings', 'icon' => 'fas fa-sms'],
            'admin.settings.email.edit' => ['label' => 'Email Settings', 'icon' => 'fas fa-envelope'],
            'admin.settings.backup.edit' => ['label' => 'Backup & Security', 'icon' => 'fas fa-database'],
        ];

        /*
                                |--------------------------------------------------------------------------
                                | DETERMINE CURRENT PAGE TITLE
                                |--------------------------------------------------------------------------
                                | This finds the currently active route's label from the $pages array.
        */
$currentRoute = collect($pages)->first(fn($data, $route) => request()->routeIs($route))['label'] ?? null;
    @endphp

    {{-- 
        BREADCRUMB + PAGE TITLE
        Show breadcrumb navigation and set the page title dynamically
        based on the current settings page.
    --}}
    @if ($currentRoute)
        <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('admin.dashboard')], ['label' => $currentRoute]]" />
        <x-slot:title>{{ $currentRoute }}</x-slot:title>
    @endif

    {{-- 
        MAIN SETTINGS PAGE GRID
        Left column: Navigation
        Right column: Content Slot
    --}}
    <div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- 
                SETTINGS NAVIGATION
                Loops through $pages array to generate buttons.
                Highlights the active page with a different style.
            --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4">Settings Categories</h3>
                    <nav class="space-y-2">
                        @foreach ($pages as $route => $data)
                            <button type="button" onclick="window.location.href='{{ route($route) }}'"
                                class="w-full text-left px-4 py-3 rounded-lg transition duration-300
                                    {{ request()->routeIs($route) ? 'bg-accent-500 text-white' : 'hover:bg-gray-100' }}">
                                <i class="{{ $data['icon'] }} mr-3"></i>
                                {{ $data['label'] }}
                            </button>
                        @endforeach
                    </nav>
                </div>
            </div>

            {{-- 
                SETTINGS CONTENT SLOT
                This will be replaced by each individual settings page's content.
            --}}
            <div class="lg:col-span-2">
                {{ $slot }}
            </div>
        </div>
    </div>
</x-admin.layout.app>
