<x-admin.layout.app>

    @php

        $pages = [
            'admin.settings.general.edit' => ['label' => 'General Settings', 'icon' => 'fas fa-cog'],
            'admin.settings.payments.edit' => ['label' => 'Payment Settings', 'icon' => 'fas fa-money-bill-wave'],
            'admin.settings.contact-info.edit' => ['label' => 'Contact Info Settings', 'icon' => 'fas fa-address-book'],
            'admin.settings.social-media.edit' => ['label' => 'Social Media Settings', 'icon' => 'fas fa-share-alt'],
            'admin.settings.sms.edit' => ['label' => 'SMS Settings', 'icon' => 'fas fa-sms'],
            'admin.settings.email.edit' => ['label' => 'Email Settings', 'icon' => 'fas fa-envelope'],
            'admin.settings.backup.edit' => ['label' => 'Backup & Security', 'icon' => 'fas fa-database'],
        ];
        $currentRoute = collect($pages)->first(fn($data, $route) => request()->routeIs($route))['label'] ?? null;

    @endphp

    @if ($currentRoute)
        <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('admin.dashboard')], ['label' => $currentRoute]]" />
        <x-slot:title>{{ $currentRoute }}</x-slot:title>
    @endif

    <div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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

            <div class="lg:col-span-2">
                {{ $slot }}
            </div>
        </div>
    </div>
</x-admin.layout.app>
