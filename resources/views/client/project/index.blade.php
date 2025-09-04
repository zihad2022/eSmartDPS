<x-client.layout.app>
    @php
        // Get the "status" query parameter from the request (example: 'active', 'cancelled', 'completed')
        $status = request()->status;

        // Map of status values to page titles
        $titleMap = [
            'active' => 'Active Projects',
            'cancelled' => 'Cancelled Projects',
            'completed' => 'Completed Projects',
        ];

        // Page title is based on the status filter; fallback to "All Projects"
        $pageTitle = $titleMap[$status] ?? 'All Projects';

        // Breadcrumb items (always show Dashboard > All Projects)
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('client.dashboard')],
            ['label' => 'All Projects', 'url' => route('client.projects.index')],
        ];

        // If a valid status filter exists, append a breadcrumb for that filter
        if (isset($titleMap[$status])) {
            $breadcrumbItems[] = [
                'label' => $pageTitle,
                'url' => route('client.projects.index', ['status' => $status]),
            ];
        }
    @endphp

    {{-- Set dynamic HTML page title --}}
    <x-slot:title>{{ $pageTitle }}</x-slot:title>

    {{-- Render breadcrumb navigation --}}
    <x-breadcrumb :items="$breadcrumbItems" />

    <div class="">
        {{-- Statistics Cards Section --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-6">
            {{-- Total Projects --}}
            <x-card.stat-card label="Total Projects" :value="number_format($totalProjects)" icon="fas fa-project-diagram" iconBgColor="bg-accent-100"
                iconTextColor="text-accent-600" />

            {{-- Active Projects --}}
            <x-card.stat-card label="Active Projects" :value="number_format($activeProjects)" icon="fas fa-play-circle" iconBgColor="bg-green-100"
                iconTextColor="text-green-600" />

            {{-- Total Investment --}}
            <x-card.stat-card label="Total Investment" :value="$settings->currency .' '. number_format($totalInvestmentAmount)" icon="fas fa-dollar-sign" iconBgColor="bg-secondary-100"
                iconTextColor="text-secondary-600" />

            {{-- Total Profits --}}
            <x-card.stat-card label="Total Profits" :value="$settings->currency .' '. number_format($totalExpectedReturn)" icon="fas fa-chart-line" iconBgColor="bg-purple-100"
                iconTextColor="text-purple-600" />

        </div>

        {{-- Projects Table Section --}}
        <div class="bg-white rounded-xl shadow-sm">
            
            {{-- Table Header with Add Button --}}
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-primary-900">All Projects</h3>
                <a href="{{ route('client.projects.create') }}"
                    class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    Add Project
                </a>
            </div>

            {{-- Projects Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                    {{-- Table Headings --}}
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">SL</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Invested Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Expected Return</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Progress</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>

                    {{-- Table Body --}}
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($projects as $index => $project)
                            <tr class="hover:bg-gray-50">
                                {{-- Serial Number --}}
                                <td class="px-6 py-4 text-sm text-primary-900 font-mono">#{{ $index + 1 }}</td>

                                {{-- Project Name --}}
                                <td class="px-6 py-4 text-sm font-medium text-primary-900">
                                    {{ $project->name }}
                                </td>

                                {{-- Category --}}
                                <td class="px-6 py-4 text-sm text-primary-600">
                                    {{ $project->category->name ?? 'N/A' }}
                                </td>

                                {{-- Invested Amount --}}
                                <td class="px-6 py-4 text-sm font-semibold text-primary-900">
                                    {{ $settings->currency }} {{ number_format($project->investment_amount) }}
                                </td>

                                {{-- Expected Return (percentage) --}}
                                <td class="px-6 py-4 text-sm font-semibold text-green-600">
                                    {{ $project->expected_return }}%
                                </td>

                                {{-- Duration --}}
                                <td class="px-6 py-4 text-sm text-primary-600">
                                    {{ $project->duration ?? 'N/A' }}
                                </td>

                                {{-- Status Badge --}}
                                <td class="px-6 py-4">
                                    @php
                                        $status = \App\Enums\ProjectStatus::from($project->status);
                                        $statusLabel = $status->label();
                                        $statusClasses = match ($status) {
                                            \App\Enums\ProjectStatus::ACTIVE => 'bg-green-100 text-green-800',
                                            \App\Enums\ProjectStatus::COMPLETED => 'bg-gray-100 text-gray-800',
                                            \App\Enums\ProjectStatus::CANCELLED => 'bg-red-100 text-red-800',
                                        };
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClasses }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                {{-- Progress Bar --}}
                                <td class="px-6 py-4">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        @php
                                            $progress = $project->progress_percent ?? 0;
                                            $progressColor = match (true) {
                                                $progress >= 80 => 'bg-green-500',
                                                $progress >= 50 => 'bg-yellow-500',
                                                $progress > 0 => 'bg-red-500',
                                                default => 'bg-gray-300',
                                            };
                                        @endphp
                                        <div class="h-2 rounded-full {{ $progressColor }}" style="width: {{ $progress }}%"></div>
                                    </div>
                                </td>

                                {{-- Action Buttons (View, Edit, Delete) --}}
                                <td class="px-6 py-4 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        {{-- View --}}
                                        <a href="{{ route('client.projects.show', $project) }}"
                                            class="text-accent-600 hover:text-accent-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        {{-- Edit --}}
                                        <a href="{{ route('client.projects.edit', $project) }}"
                                            class="text-secondary-600 hover:text-secondary-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        {{-- Delete --}}
                                        <form action="{{ route('client.projects.destroy', $project) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-600 hover:text-red-900 delete-btn">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    {{-- Confirmation Modal --}}
                                    <x-confirm-modal />
                                </td>
                            </tr>
                        @empty
                            {{-- If no projects exist --}}
                            <tr>
                                <td colspan="9" class="text-center py-4 text-primary-500">
                                    No projects found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-primary-600">
                        @if ($projects->total() > 0)
                            Showing {{ $projects->firstItem() }} to {{ $projects->lastItem() }} of {{ $projects->total() }} results
                        @else
                            No results found.
                        @endif
                    </div>
                    <div class="flex space-x-2">
                        <x-pagination :paginator="$projects" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-client.layout.app>
