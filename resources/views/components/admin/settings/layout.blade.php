<x-admin.layout.app>
    @php
        $pages = [
            'admin.settings.general.edit' => 'General Settings',
            'admin.settings.payments.edit' => 'Payment Settings',
            'admin.settings.sms.edit' => 'SMS Settings',
            'admin.settings.email.edit' => 'Email Settings',
            'admin.settings.backup.edit' => 'Backup & Security',
        ];

        $currentRoute = collect($pages)->first(fn($label, $route) => request()->routeIs($route));
    @endphp

    @if ($currentRoute)
        <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('admin.dashboard')], ['label' => $currentRoute]]" />
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
                        <button type="button" onclick="window.location.href='{{ route('admin.settings.general.edit') }}'"
                            class="w-full text-left px-4 py-3 rounded-lg transition duration-300 
                               {{ request()->routeIs('admin.settings.general.edit') ? 'bg-accent-500 text-white active' : '' }}">
                            <i class="fas fa-cog mr-3"></i> General Settings
                        </button>
                        {{-- <button type="button" onclick="window.location.href='{{ route('admin.settings.shares.edit') }}'"
                            class="w-full text-left px-4 py-3 rounded-lg transition duration-300 
                               {{ request()->routeIs('admin.settings.shares.edit') ? 'bg-accent-500 text-white active' : '' }}">
                            <i class="fas fa-chart-pie mr-3"></i>Share Settings
                        </button> --}}
                        <button type="button"
                            onclick="window.location.href='{{ route('admin.settings.payments.edit') }}'"
                            class="w-full text-left px-4 py-3 rounded-lg transition duration-300 
                               {{ request()->routeIs('admin.settings.payments.edit') ? 'bg-accent-500 text-white active' : '' }}">
                            <i class="fas fa-money-bill-wave mr-3"></i>Payment Settings
                        </button>
                        <button type="button" onclick="window.location.href='{{ route('admin.settings.sms.edit') }}'"
                            class="w-full text-left px-4 py-3 rounded-lg transition duration-300 
                               {{ request()->routeIs('admin.settings.sms.edit') ? 'bg-accent-500 text-white active' : '' }}">
                            <i class="fas fa-sms mr-3"></i>SMS Settings
                        </button>
                        <button type="button" onclick="window.location.href='{{ route('admin.settings.email.edit') }}'"
                            class="w-full text-left px-4 py-3 rounded-lg transition duration-300 
                               {{ request()->routeIs('admin.settings.email.edit') ? 'bg-accent-500 text-white active' : '' }}">
                            <i class="fas fa-envelope mr-3"></i>Email Settings
                        </button>
                        <button type="button"
                            onclick="window.location.href='{{ route('admin.settings.backup.edit') }}'"
                            class="w-full text-left px-4 py-3 rounded-lg transition duration-300 
                               {{ request()->routeIs('admin.settings.backup.edit') ? 'bg-accent-500 text-white active' : '' }}">
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
</x-admin.layout.app>
