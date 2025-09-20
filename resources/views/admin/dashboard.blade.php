<x-admin.layout.app>
    <x-slot:title>Dashboard</x-slot:title>

    <!-- Welcome Banner -->
    <x-admin.sections.dashboard.welcome :admin="$admin" />

    <!-- Stats Cards -->
    <x-admin.sections.dashboard.stats :totalClients="$totalClients" :totalBalance="$totalBalance" :totalUsers="$totalUsers" :totalPackages="$totalPackages"/>

    <!-- Charts and Recent Activities -->
    <x-admin.sections.dashboard.chart :chartData="$chartData" :recentActivities="$recentActivities" />

    <!-- Recent Payments and Projects -->
    <x-admin.sections.dashboard.payment :recentPayments="$recentPayments" />
</x-admin.layout.app>
