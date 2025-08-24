<x-client.layout.app>
    <!-- Members Content -->
    <div class="">
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-primary-500 font-medium">Total Projects</p>
                        <h3 class="text-xl md:text-2xl font-bold text-primary-900">{{ $totalProjects }}</h3>
                    </div>
                    <div class="w-10 h-10 bg-accent-100 text-accent-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-project-diagram text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-primary-500 font-medium">Active Projects</p>
                        <h3 class="text-xl md:text-2xl font-bold text-primary-900">{{ $activeProjects }}</h3>
                    </div>
                    <div class="w-10 h-10 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-play-circle text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-primary-500 font-medium">Total Invested</p>
                        <h3 class="text-xl md:text-2xl font-bold text-primary-900">${{ $totalInvestmentAmount }}</h3>
                    </div>
                    <div
                        class="w-10 h-10 bg-secondary-100 text-secondary-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-primary-500 font-medium">Total Profits</p>
                        <h3 class="text-xl md:text-2xl font-bold text-primary-900">${{ $totalExpectedReturn }}</h3>
                    </div>
                    <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Responsive table wrapper with horizontal scroll --}}
        <div class="bg-white rounded-xl shadow-sm">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-primary-900">All Projects</h3>
        <a href="{{ route('client.projects.create') }}"
            class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            Add Project
        </a>
    </div>

    {{-- Responsive table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
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
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($projects as $index => $project)
                    <tr class="hover:bg-gray-50">
                        {{-- SL --}}
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
                            ${{ number_format($project->investment_amount, 2) }}
                        </td>

                        {{-- Expected Return --}}
                        <td class="px-6 py-4 text-sm font-semibold text-green-600">
                            {{ $project->expected_return }}%
                        </td>

                        {{-- Duration --}}
                        <td class="px-6 py-4 text-sm text-primary-600">
                            {{ $project->duration ?? 'N/A' }}
                        </td>

                        {{-- Status --}}
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

                        {{-- Progress --}}
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

                        {{-- Actions --}}
                        <td class="px-6 py-4 text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('client.projects.show', $project) }}"
                                    class="text-accent-600 hover:text-accent-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('client.projects.edit', $project) }}"
                                    class="text-secondary-600 hover:text-secondary-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('client.projects.destroy', $project) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="text-red-600 hover:text-red-900 delete-btn">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        <x-confirm-modal />
                    </tr>
                @empty
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
                    Showing {{ $projects->firstItem() }} to {{ $projects->lastItem() }} of
                    {{ $projects->total() }} results
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
