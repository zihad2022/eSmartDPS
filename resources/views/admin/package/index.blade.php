<x-admin.layout.app>
    @php
        // -----------------------------
        // 1. Page Setup
        // -----------------------------
        // Resolve current status and page metadata.
        $status = request()->status;
        $titleMap = [
            'active' => 'Active Packages',
            'inactive' => 'Inactive Packages',
        ];
        $pageTitle = $titleMap[$status] ?? 'All Packages';

        // -----------------------------
        // 2. Breadcrumbs
        // -----------------------------
        // Build breadcrumb trail dynamically based on status filter.
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'All Packages', 'url' => route('admin.packages.index')],
        ];
        if (isset($titleMap[$status])) {
            $breadcrumbItems[] = [
                'label' => $pageTitle,
                'url' => route('admin.packages.index', ['status' => $status]),
            ];
        }
    @endphp

    {{-- ----------------------------- 
         3. Page Title & Breadcrumb 
         ----------------------------- --}}
    <x-slot:title>{{ $pageTitle }}</x-slot:title>
    <x-breadcrumb :items="$breadcrumbItems" />

    {{-- ----------------------------- 
         4. Flash Messages 
         ----------------------------- --}}
    <div>
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        {{-- ----------------------------- 
             5. Stats Cards 
             ----------------------------- --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 md:gap-6 mb-6">
            <x-card.stat-card label="Total Packages" :value="number_format($packages->total())" iconBgColor="bg-primary-100"
                iconTextColor="text-primary-600" icon="fas fa-file-invoice-dollar" />

            <x-card.stat-card label="Active Packages" :value="number_format($activePackages)" iconBgColor="bg-green-100"
                iconTextColor="text-green-600" icon="fas fa-check-circle" />

            <x-card.stat-card label="Inactive Packages" :value="number_format($inactivePackages)" iconBgColor="bg-red-100"
                iconTextColor="text-red-600" icon="fas fa-times-circle" />

            <x-card.stat-card label="Monthly Package" :value="number_format($monthlyPackages)" iconBgColor="bg-blue-100"
                iconTextColor="text-blue-600" icon="fas fa-calendar-alt" />

            <x-card.stat-card label="Yearly Package" :value="number_format($yearlyPackages)" iconBgColor="bg-purple-100"
                iconTextColor="text-purple-600" icon="fas fa-calendar" />
        </div>

        {{-- ----------------------------- 
            6. Packages Table (component-based)
            ----------------------------- --}}
        <x-data-table
            :page-title="$pageTitle"
            :rows="$packages"
            :headers="['SL','Name','Price','Discount','Billing Cycle','Member Limit','User Limit','Project Limit','Status','Trial','Created At','Actions']"
            row-view="admin.package.partials.row"
        >
            <x-slot:actions>
                {{-- Search Form --}}
                <form method="GET" action="{{ route('admin.packages.index') }}" class="relative w-full md:w-auto">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search packages..."
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-accent-500 transition duration-300">
                    <button type="submit"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-accent-500 transition">
                        <i class="fas fa-search"></i>
                    </button>
                </form>

                @adminCan('create packages')
                    <a href="{{ route('admin.packages.create') }}"
                        class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                        Add Package
                    </a>
                @endadminCan

                @adminCan('export packages')
                    <a href="{{ route('admin.packages.export', ['status' => $status]) }}"
                        class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                        <i class="fas fa-download mr-2"></i>Export
                    </a>
                @endadminCan
            </x-slot:actions>
        </x-data-table>
    </div>
</x-admin.layout.app>
