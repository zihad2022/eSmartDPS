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
                        <h3 class="text-xl md:text-2xl font-bold text-primary-900">$8,750</h3>
                    </div>
                    <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Projects Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <!-- Project Card 1 -->
            @forelse ($projects as $project)
                <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition duration-300">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-primary-900">{{ $project->name }}</h3>
                            <p class="text-sm text-primary-600">{{ $project->category->name }}</p>
                        </div>
                        @php
                            $status = \App\Enums\ProjectStatus::from($project->status);
                            $statusLabel = $status->label();
                            $statusClasses = match ($status) {
                                \App\Enums\ProjectStatus::ACTIVE => 'bg-green-100 text-green-800',
                                \App\Enums\ProjectStatus::COMPLETED => 'bg-gray-100 text-gray-800',
                                \App\Enums\ProjectStatus::CANCELLED => 'bg-red-100 text-red-800',
                            };
                        @endphp
                        <span class="px-3 py-1 text-xs font-medium rounded-full {{ $statusClasses }}">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-primary-600">Invested Amount:</span>
                            <span
                                class="font-medium text-primary-900">${{ number_format($project->investment_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-primary-600">Expected Return:</span>
                            <span class="font-medium text-green-600">{{ $project->expected_return }}%</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-primary-600">Duration:</span>
                            <span class="font-medium text-primary-900">
                                {{ $project->duration ?? 'N/A' }}
                            </span>
                        </div>
                    </div>

                    <!-- Optional: Progress Bar -->
                    @php
                        $progress = $project->progress_percent ?? 0;

                        $progressColor = match (true) {
                            $progress >= 80 => 'bg-green-500',
                            $progress >= 50 => 'bg-yellow-500',
                            $progress > 0 => 'bg-red-500',
                            default => 'bg-gray-300',
                        };
                    @endphp

                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-primary-600">Progress</span>
                            <span class="font-medium text-primary-900">
                                {{ $project->progress_percent !== null ? $progress . '%' : 'N/A' }}
                            </span>
                        </div>

                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="progress-bar h-2 rounded-full transition-all duration-300 {{ $progressColor }}"
                                style="width: {{ $progress }}%">
                            </div>
                        </div>
                    </div>


                    <div class="flex space-x-2">
                        <button
                            class="flex-1 bg-accent-100 text-accent-600 px-3 py-2 rounded-lg text-sm font-medium hover:bg-accent-200 transition duration-300">
                            View Details
                        </button>
                        <a href="{{ route('client.projects.edit', $project) }}"
                            class="px-3 py-2 text-primary-600 hover:text-primary-800 transition duration-300">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-1 md:col-span-2 lg:col-span-3 flex flex-col items-center justify-center text-center bg-white rounded-xl p-8 shadow-sm border border-dashed border-primary-200">
                    <svg class="w-12 h-12 mb-4 text-primary-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.75 9.75h.008v.008H9.75V9.75zm4.5 0h.008v.008h-.008V9.75zm-.918 5.583a4.5 4.5 0 01-6.624-5.799M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-primary-800 mb-2">No Projects Found</h3>
                    <p class="text-sm text-primary-600 mb-4">It looks like you haven’t added any projects yet. Start by
                        creating a new one!</p>
                    <a href="{{ route('client.projects.create') }}"
                        class="inline-block bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                        Add New Project
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</x-client.layout.app>
