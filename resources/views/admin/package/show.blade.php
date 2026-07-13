<x-admin.layout.app>
    <x-slot:title>{{ $package->name }}</x-slot:title>

    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Packages', 'url' => route('admin.packages.index')],
        ['label' => $package->name],
    ]" />

    <div class="space-y-6">
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'"
                :title="session('success') ? 'Success' : 'Error'"
                :message="session('success') ?? session('error')" />
        @endif

        <div class="rounded-xl bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-2xl font-bold text-primary-900">{{ $package->name }}</h2>
                        <span class="rounded-full px-3 py-1 text-xs font-medium {{ $package->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $package->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-primary-600">
                        {{ $package->description ?: 'No package description has been added.' }}
                    </p>
                </div>

                @adminCan('edit packages')
                    <a href="{{ route('admin.packages.edit', $package) }}"
                        class="inline-flex items-center justify-center rounded-lg bg-accent-500 px-4 py-2 text-sm font-medium text-white hover:bg-accent-600">
                        <i class="fas fa-edit mr-2"></i>Edit Package
                    </a>
                @endadminCan
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <x-card.stat-card label="Base Price" :value="$currency.' '.number_format($package->price)"
                icon="fas fa-coins" iconBgColor="bg-blue-100" iconTextColor="text-blue-600" />
            <x-card.stat-card label="Final Price" :value="$currency.' '.number_format($package->final_price)"
                icon="fas fa-tag" iconBgColor="bg-green-100" iconTextColor="text-green-600" />
            <x-card.stat-card label="Active Subscriptions" :value="number_format($package->active_subscriptions_count)"
                icon="fas fa-user-check" iconBgColor="bg-purple-100" iconTextColor="text-purple-600" />
            <x-card.stat-card label="Invoices" :value="number_format($package->invoices_count)"
                icon="fas fa-file-invoice" iconBgColor="bg-yellow-100" iconTextColor="text-yellow-600" />
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-xl bg-white p-6 shadow-sm">
                <h3 class="mb-5 text-lg font-semibold text-primary-900">Pricing & Billing</h3>
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-primary-500">Billing Cycle</dt>
                        <dd class="mt-1 font-medium text-primary-900">{{ $package->billing_cycle?->label() ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-primary-500">Discount</dt>
                        <dd class="mt-1 font-medium text-primary-900">
                            @if ($package->discount_amount > 0)
                                {{ $package->discount_type === \App\Enums\Package\DiscountType::PERCENT
                                    ? number_format($package->discount_value).'%'
                                    : $currency.' '.number_format($package->discount_value) }}
                            @else
                                None
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-primary-500">Trial</dt>
                        <dd class="mt-1 font-medium text-primary-900">{{ $package->has_trial ? $package->trial_days.' days' : 'Not available' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-primary-500">All Subscriptions</dt>
                        <dd class="mt-1 font-medium text-primary-900">{{ number_format($package->subscriptions_count) }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm">
                <h3 class="mb-5 text-lg font-semibold text-primary-900">Usage Limits</h3>
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-primary-500">Members</dt>
                        <dd class="mt-1 text-xl font-semibold text-primary-900">{{ $package->member_limit ?: 'Unlimited' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-primary-500">Users</dt>
                        <dd class="mt-1 text-xl font-semibold text-primary-900">{{ $package->user_limit ?: 'Unlimited' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-primary-500">Projects</dt>
                        <dd class="mt-1 text-xl font-semibold text-primary-900">{{ $package->project_limit ?: 'Unlimited' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        @if (! empty($package->features))
            <div class="rounded-xl bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-lg font-semibold text-primary-900">Features</h3>
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($package->features as $feature => $enabled)
                        <div class="flex items-center gap-3 rounded-lg border border-gray-200 p-3">
                            <i class="fas {{ $enabled ? 'fa-check-circle text-green-500' : 'fa-times-circle text-gray-400' }}"></i>
                            <span class="text-sm text-primary-700">{{ str($feature)->replace(['_', '-'], ' ')->title() }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-admin.layout.app>
