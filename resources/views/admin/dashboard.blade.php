<x-admin.layout.app>
    <x-slot:title>Dashboard</x-slot:title>

    <!-- Welcome Banner -->
    <x-admin.sections.dashboard.welcome :admin="$admin" />

    <!-- Stats Cards -->
    <x-admin.sections.dashboard.stats :totalClients="$totalClients" :totalBalance="$totalBalance" />

    <!-- Charts and Recent Activities -->
    <x-admin.sections.dashboard.chart :chartData="$chartData" />

    <!-- Recent Payments and Projects -->
    <x-admin.sections.dashboard.payment />
</x-admin.layout.app>
