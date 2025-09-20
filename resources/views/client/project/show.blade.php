<x-client.layout.app>
    @php
        /**
         * =======================================
         * Page Setup: Title, Breadcrumbs
         * =======================================
         */

        // 1. Set page title
        $pageTitle = 'Project Details';

        // 2. Breadcrumb navigation items
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('client.dashboard')],
            ['label' => 'All Projects', 'url' => route('client.projects.index')],
            ['label' => 'Project Details'],
        ];
    @endphp

    {{-- ===========================
         Set HTML Page Title
    ============================ --}}
    <x-slot:title>{{ $pageTitle }}</x-slot:title>

    {{-- ===========================
         Breadcrumb Navigation
    ============================ --}}
    <x-breadcrumb :items="$breadcrumbItems" />

    {{-- ===========================
         Main Content Wrapper
    ============================ --}}
    <div class="max-w-5xl mx-auto">

        {{-- ===========================
             Project Header Section
        ============================ --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                {{-- Project Name & Category --}}
                <div>
                    <h2 class="text-2xl font-bold text-primary-900 mb-1">{{ $project->name }}</h2>
                    <p class="text-sm text-primary-600">{{ $project->category->name ?? 'Uncategorized' }}</p>
                </div>

                {{-- Project Status Badge --}}
                <span class="px-3 py-1 text-xs font-medium rounded-full
                    {{ $project->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $project->status ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>

        {{-- ===========================
             Project Info Cards Section
        ============================ --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {{-- Investment Amount --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-sm font-medium text-primary-600 mb-2">Investment Amount</h3>
                <p class="text-xl font-semibold text-primary-900">${{ number_format($project->investment_amount) }}</p>
            </div>

            {{-- Expected Return --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-sm font-medium text-primary-600 mb-2">Expected Return</h3>
                <p class="text-xl font-semibold text-green-600">{{ $project->expected_return }}%</p>
            </div>

            {{-- Start Date --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-sm font-medium text-primary-600 mb-2">Start Date</h3>
                <p class="text-lg font-semibold text-primary-900">
                    {{ \Carbon\Carbon::parse($project->start_date)->format('F d, Y') }}
                </p>
            </div>

            {{-- End Date --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-sm font-medium text-primary-600 mb-2">End Date</h3>
                <p class="text-lg font-semibold text-primary-900">
                    {{ \Carbon\Carbon::parse($project->end_date)->format('F d, Y') }}
                </p>
            </div>
        </div>

        {{-- ===========================
             Project Progress Bar Section
        ============================ --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            {{-- Label + Percentage --}}
            <div class="flex justify-between text-sm mb-2">
                <span class="text-primary-600">Progress</span>
                <span class="font-medium text-primary-900">{{ $project->progress_percent ?? 0 }}%</span>
            </div>

            {{-- Determine progress bar color --}}
            @php
                $progress = $project->progress_percent ?? 0;
                $progressColor = match (true) {
                    $progress >= 80 => 'bg-green-500',
                    $progress >= 50 => 'bg-yellow-500',
                    $progress > 0 => 'bg-red-500',
                    default => 'bg-gray-300',
                };
            @endphp

            {{-- Progress Bar Visualization --}}
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="h-2 rounded-full {{ $progressColor }}" style="width: {{ $progress }}%"></div>
            </div>
        </div>

        {{-- ===========================
             Project Description Section
        ============================ --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-primary-900 mb-2">Description</h3>
            <p class="text-sm text-primary-700 leading-relaxed">
                {{ $project->description ?? 'No description available.' }}
            </p>
        </div>
    </div>
</x-client.layout.app>
