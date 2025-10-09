<x-admin.layout.app>
    @php
        // -----------------------------
        // 1. Page Setup
        // -----------------------------
        // Fetch settings (currency, etc.), current status filter,
        // and map titles/breadcrumbs accordingly.
        $settings = \App\Models\AdminSetting::first();
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
             6. Packages Table Container 
             ----------------------------- --}}
        <div class="bg-white rounded-xl shadow-sm">

            {{-- -------------------------------------- 
                 6.1. Table Header (Title + Actions) 
                 -------------------------------------- --}}
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">
                        {{ $pageTitle }}
                    </h3>
                    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">

                        {{-- Search Form --}}
                        <form method="GET" action="{{ route('admin.packages.index') }}"
                            class="relative w-full md:w-auto">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search packages..."
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-accent-500 transition duration-300">
                            <button type="submit"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-accent-500 transition">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>

                        {{-- Add Package --}}
                        <a href="{{ route('admin.packages.create') }}"
                            class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                            Add Package
                        </a>

                        {{-- Export Packages --}}
                        <a href="{{ route('admin.packages.export', ['status' => $status]) }}"
                            class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                            <i class="fas fa-download mr-2"></i>Export
                        </a>
                    </div>
                </div>
            </div>

            {{-- ----------------------------- 
                 6.2. Packages Table 
                 ----------------------------- --}}
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                #Sl</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Name</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Price</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Discount</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Billing Cycle</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Member Limit</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                User Limit</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Project Limit</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Trial</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Created At</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $sl = $packages->firstItem(); @endphp
                        @forelse ($packages as $package)
                            <tr class="hover:bg-gray-50">
                                {{-- Serial Number --}}
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">#{{ $sl++ }}</td>

                                {{-- Package Name --}}
                                <td class="px-6 py-4 text-sm text-primary-900 font-semibold">{{ $package->name }}</td>

                                {{-- Package Price --}}
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">
                                    {{ $settings->currency ?? '$' }} {{ number_format($package->price) }}
                                </td>

                                {{-- Package Discount --}}
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">
                                    @if ($package->discount_value > 0)
                                        @if ($package->discount_type === \App\Enums\Package\DiscountType::FIXED)
                                            {{ $settings->currency ?? '$' }}
                                            {{ number_format($package->discount_value) }}
                                        @else
                                            {{ $package->discount_value }}%
                                        @endif
                                    @else
                                        <span class="text-gray-500">N/A</span>
                                    @endif
                                </td>

                                {{-- Billing Cycle --}}
                                <td class="px-6 py-4 text-sm text-primary-900 font-semibold">
                                    {{ $package->billing_cycle ? $package->billing_cycle->label() : '—' }}
                                </td>

                                {{-- Limits --}}
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">{{ $package->member_limit }}
                                </td>
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">{{ $package->user_limit }}
                                </td>
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">{{ $package->project_limit }}
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full 
                                        {{ $package->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $package->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                {{-- Trial --}}
                                <td class="px-6 py-4 text-sm text-primary-900 font-semibold">
                                    @if ($package->has_trial)
                                        {{ $package->trial_days }} days
                                    @else
                                        <span class="text-gray-500">N/A</span>
                                    @endif
                                </td>

                                {{-- Created At --}}
                                <td class="px-6 py-4 text-sm text-primary-600">
                                    {{ $package->created_at->format('M d, Y') }}</td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        {{-- Edit --}}
                                        <a href="{{ route('admin.packages.edit', $package->id) }}"
                                            class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- Delete --}}
                                        <form method="POST"
                                            action="{{ route('admin.packages.destroy', $package->id) }}"
                                            class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-600 hover:text-red-900 delete-btn"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- No Packages --}}
                            <tr>
                                <td colspan="12" class="text-center py-4 text-sm text-gray-500">No packages found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ----------------------------- 
                 6.3. Pagination 
                 ----------------------------- --}}
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    {{-- Showing Results --}}
                    <div class="text-sm text-primary-600">
                        @if ($packages->total() > 0)
                            Showing {{ $packages->firstItem() }} to {{ $packages->lastItem() }} of
                            {{ $packages->total() }} results
                        @else
                            No results found.
                        @endif
                    </div>

                    {{-- Pagination Links --}}
                    <div class="flex space-x-2">
                        <x-pagination :paginator="$packages" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin.layout.app>
