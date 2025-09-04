<x-client.layout.app>
    @php
        $pages = [
            'client.settings.general.edit' => 'General Settings',
            'client.settings.share.edit' => 'Share Settings',
            'client.settings.payment.edit' => 'Payment Settings',
            'client.settings.notification.edit' => 'Notification Settings',
            'client.settings.backup-security.edit' => 'Backup & Security',
        ];

        $currentRoute = collect($pages)->first(fn($label, $route) => request()->routeIs($route));
    @endphp

    @if ($currentRoute)
        <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('client.dashboard')], ['label' => $currentRoute]]" />
        <x-slot:title>{{ $currentRoute }}</x-slot:title>
    @endif

    <!-- Settings Content -->
    <div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Settings Navigation -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4">Settings Categories</h3>
                    <nav class="space-y-2">
                        <button type="button"
                            onclick="window.location.href='{{ route('client.settings.general.edit') }}'"
                            class="w-full text-left px-4 py-3 rounded-lg transition duration-300 
                               {{ request()->routeIs('client.settings.general.edit') ? 'bg-accent-500 text-white active' : '' }}">
                            <i class="fas fa-cog mr-3"></i> General Settings
                        </button>
                        <button type="button"
                            onclick="window.location.href='{{ route('client.settings.share.edit') }}'"
                            class="w-full text-left px-4 py-3 rounded-lg transition duration-300 
                               {{ request()->routeIs('client.settings.share.edit') ? 'bg-accent-500 text-white active' : '' }}">
                            <i class="fas fa-chart-pie mr-3"></i>Share Settings
                        </button>
                        <button type="button" onclick="window.location.href='{{ route('client.settings.payment.edit') }}'"
                            class="w-full text-left px-4 py-3 rounded-lg transition duration-300 
                               {{ request()->routeIs('client.settings.payment.edit') ? 'bg-accent-500 text-white active' : '' }}">
                            <i class="fas fa-money-bill-wave mr-3"></i>Payment Settings
                        </button>
                        <button type="button" onclick="window.location.href='{{ route('client.settings.notification.edit') }}'"
                            class="w-full text-left px-4 py-3 rounded-lg transition duration-300 
                               {{ request()->routeIs('client.settings.notification.edit') ? 'bg-accent-500 text-white active' : '' }}">
                            <i class="fas fa-bell mr-3"></i>Notification Settings
                        </button>
                        <button type="button" onclick="window.location.href='{{ route('client.settings.backup-security.edit') }}'"
                            class="w-full text-left px-4 py-3 rounded-lg transition duration-300 
                               {{ request()->routeIs('client.settings.backup-security.edit') ? 'bg-accent-500 text-white active' : '' }}">
                            <i class="fas fa-database mr-3"></i>Backup & Security
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Settings Content -->
            <div class="lg:col-span-2">
                {{ $slot }}
            </div>
        </div>
    </div>
</x-client.layout.app>
