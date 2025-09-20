<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
    <x-card.stat-card :label="'Total Clients'" :value="$totalClients" :bgColor="'bg-blue-100'" :textColor="'text-blue-600'" :iconBgColor="'bg-blue-100'"
        :iconTextColor="'text-blue-600'" :icon="'fas fa-users'" />

    @php
        $settings = \App\Models\AdminSetting::select('currency')->first();
    @endphp
    <x-card.stat-card :label="'Total Balance'" :value="$settings->currency . ' ' . number_format($totalBalance)" :bgColor="'bg-green-100'" :textColor="'text-green-600'" :iconBgColor="'bg-green-100'"
        :iconTextColor="'text-green-600'" :icon="'fas fa-wallet'" />

    <x-card.stat-card :label="'Total Users'" :value="number_format($totalUsers)" :bgColor="'bg-purple-100'" :textColor="'text-purple-600'" :iconBgColor="'bg-purple-100'"
        :iconTextColor="'text-purple-600'" :icon="'fas fa-user-friends'" />

    <x-card.stat-card :label="'Total Packages'" :value="number_format($totalPackages)" :bgColor="'bg-yellow-100'" :textColor="'text-yellow-600'" :iconBgColor="'bg-yellow-100'"
        :iconTextColor="'text-yellow-600'" :icon="'fas fa-project-diagram'" />
</div>
