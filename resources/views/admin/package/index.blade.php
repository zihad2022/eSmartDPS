<x-admin.layout.app>
    @php
        $settings = \App\Models\AdminSetting::first();
        $status = request()->status;
        $titleMap = [
            'active' => 'Active Packages',
            'inactive' => 'Inactive Packages',
        ];

        $pageTitle = $titleMap[$status] ?? 'All Packages';

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

    <x-slot:title>{{ $pageTitle }}</x-slot:title>
    <x-breadcrumb :items="$breadcrumbItems" />


    <!-- Packages Content -->
    <div>
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-primary-500 font-medium">Total Packages</p>
                        <h3 class="text-xl md:text-2xl font-bold text-primary-900">{{ $packages->total() }}</h3>
                    </div>
                    <div class="w-10 h-10 bg-accent-100 text-accent-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-invoice-dollar text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-primary-500 font-medium">Active Packages</p>
                        <h3 class="text-xl md:text-2xl font-bold text-primary-900">
                            {{ $packages->where('is_active', true)->count() }}
                        </h3>
                    </div>
                    <div class="w-10 h-10 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-primary-500 font-medium">Monthly Package</p>
                        <h3 class="text-xl md:text-2xl font-bold text-primary-900">
                            {{ $packages->where('billing_cycle', 'monthly')->count() }}
                        </h3>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-primary-500 font-medium">Yearly Package</p>
                        <h3 class="text-xl md:text-2xl font-bold text-primary-900">
                            {{ $packages->where('billing_cycle', 'yearly')->count() }}
                        </h3>
                    </div>
                    <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Packages Table -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">
                        {{ $pageTitle }}
                    </h3>
                    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                        <a href="{{ route('admin.packages.create') }}"
                            class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                            Add Package
                        </a>
                        <a href="{{ route('admin.packages.export', ['status' => $status]) }}"
                            class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                            <i class="fas fa-download mr-2"></i>Export
                        </a>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                sl</th>
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
                                Status</th>
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
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">#{{ $sl++ }}</td>

                                <td class="px-6 py-4 text-sm text-primary-900 font-semibold">
                                    {{ $package->name }}
                                </td>

                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">
                                    {{ $settings->currency }} {{ $package->price }}
                                </td>

                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">
                                    @php
                                        $isDiscount = $package->discount_value > 0;
                                    @endphp

                                    @if ($isDiscount)
                                        {{-- If discount type is FIXED → Show currency with amount --}}
                                        @if ($package->discount_type === \App\Enums\Package\DiscountType::FIXED)
                                            {{ $settings->currency . ' ' . $package->discount_value }}
                                        @else
                                            {{-- If discount type is PERCENT → Show percentage --}}
                                            {{ $package->discount_value . '%' }}
                                        @endif
                                    @else
                                        <span class="text-gray-500">—</span>
                                    @endif
                                </td>



                                <td class="px-6 py-4 text-sm text-primary-900 font-semibold">
                                    {{ $package->billing_cycle->label() }}
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full 
                                        {{ $package->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $package->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm text-primary-600">
                                    {{ $package->created_at->format('M d, Y') }}
                                </td>

                                <td class="px-6 py-4 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.packages.edit', $package->id) }}"
                                            class="text-secondary-600 hover:text-secondary-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

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
                                <x-confirm-modal />
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-sm text-gray-500">No packages
                                    found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-primary-600">
                        @if ($packages->total() > 0)
                            Showing {{ $packages->firstItem() }} to {{ $packages->lastItem() }} of
                            {{ $packages->total() }} results
                        @else
                            No results found.
                        @endif
                    </div>
                    <div class="flex space-x-2">
                        <x-pagination :paginator="$packages" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin.layout.app>
