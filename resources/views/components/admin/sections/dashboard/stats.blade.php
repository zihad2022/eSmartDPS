<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
    <!-- Total Clients -->
    <x-card.stat-card :label="'Total Clients'" :value="$totalClients" :bgColor="'bg-primary-100'" :textColor="'text-primary-600'" :iconBgColor="'bg-primary-100'"
        :iconTextColor="'text-primary-600'" :icon="'fas fa-users'" />

    <!-- Total Balance -->
    @php
        $settings = \App\Models\AdminSetting::select('currency')->first();
    @endphp
    <x-card.stat-card :label="'Total Balance'" :value="$settings->currency . ' ' . number_format($totalBalance)" :bgColor="'bg-primary-100'" :textColor="'text-primary-600'" :iconBgColor="'bg-primary-100'"
        :iconTextColor="'text-primary-600'" :icon="'fas fa-wallet'" />
</div>
