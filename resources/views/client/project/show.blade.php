<x-client.layout.app>
    {{-- Set the page title shown in the browser/tab --}}
    <x-slot:title>Project Details</x-slot:title>

    {{-- Breadcrumb navigation for better UX --}}
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('client.dashboard')],
        ['label' => 'All Projects', 'url' => route('client.projects.index')],
        ['label' => 'Project Details'],
    ]" />

    <div class="max-w-5xl mx-auto">
        {{-- Project Header (Name, Category, Status) --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    {{-- Project name --}}
                    <h2 class="text-2xl font-bold text-primary-900 mb-1">{{ $project->name }}</h2>

                    {{-- Project category (fallback to Uncategorized) --}}
                    <p class="text-sm text-primary-600">
                        {{ $project->category->name ?? 'Uncategorized' }}
                    </p>
                </div>

                {{-- Project status badge (Active/Inactive) --}}
                <span class="px-3 py-1 text-xs font-medium rounded-full
                    {{ $project->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $project->status ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>

        {{-- Project Info Cards (Investment, Return, Dates) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {{-- Investment amount --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-sm font-medium text-primary-600 mb-2">Investment Amount</h3>
                <p class="text-xl font-semibold text-primary-900">
                    ${{ number_format($project->investment_amount) }}
                </p>
            </div>

            {{-- Expected return percentage --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-sm font-medium text-primary-600 mb-2">Expected Return</h3>
                <p class="text-xl font-semibold text-green-600">
                    {{ $project->expected_return }}%
                </p>
            </div>

            {{-- Start date --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-sm font-medium text-primary-600 mb-2">Start Date</h3>
                <p class="text-lg font-semibold text-primary-900">
                    {{ \Carbon\Carbon::parse($project->start_date)->format('F d, Y') }}
                </p>
            </div>

            {{-- End date --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-sm font-medium text-primary-600 mb-2">End Date</h3>
                <p class="text-lg font-semibold text-primary-900">
                    {{ \Carbon\Carbon::parse($project->end_date)->format('F d, Y') }}
                </p>
            </div>
        </div>

        {{-- Project Progress Bar --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            {{-- Label + percentage value --}}
            <div class="flex justify-between text-sm mb-2">
                <span class="text-primary-600">Progress</span>
                <span class="font-medium text-primary-900">{{ $project->progress_percent ?? 0 }}%</span>
            </div>

            {{-- PHP block: determine bar color dynamically --}}
            @php
                $progress = $project->progress_percent ?? 0;
                $progressColor = match (true) {
                    $progress >= 80 => 'bg-green-500',
                    $progress >= 50 => 'bg-yellow-500',
                    $progress > 0 => 'bg-red-500',
                    default => 'bg-gray-300',
                };
            @endphp

            {{-- Progress bar visualization --}}
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="h-2 rounded-full {{ $progressColor }}" style="width: {{ $progress }}%"></div>
            </div>
        </div>

        {{-- Project Description --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-primary-900 mb-2">Description</h3>
            <p class="text-sm text-primary-700 leading-relaxed">
                {{ $project->description ?? 'No description available.' }}
            </p>
        </div>
    </div>
</x-client.layout.app>
