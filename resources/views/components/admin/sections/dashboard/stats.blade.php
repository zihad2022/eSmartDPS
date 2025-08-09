<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
    <!-- Total Clients -->
    <x-card.stat-card :label="'Total Clients'" :value="$totalClients" :bgColor="'bg-primary-100'" :textColor="'text-primary-600'" :icon="'fas fa-users'" />
    <!-- Total Balance -->
    @php
        $settings = \App\Models\AdminSetting::select('currency')->first();
    @endphp
    <x-card.stat-card :label="'Total Balance'" :value="$settings->currency . ' ' . $totalBalance" :bgColor="'bg-primary-100'" :textColor="'text-primary-600'" :icon="'fas fa-wallet'" />

    <!-- Total Investments -->
    <x-card.stat-card :label="'Total Investments'" :value="18200" :bgColor="'bg-purple-100'" :textColor="'text-purple-600'" :icon="'fas fa-chart-line'" />
    <!-- Total Profits -->
    <x-card.stat-card :label="'Total Profits'" :value="3750" :bgColor="'bg-green-100'" :textColor="'text-green-600'" :icon="'fas fa-trophy'" />
</div>
