<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Financial Chart -->
    <div class="bg-white rounded-xl shadow-sm p-6 lg:col-span-2 dashboard-card">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-primary-900">Financial Overview</h3>
            <div class="flex space-x-2">
                <button class="px-3 py-1 text-xs font-medium bg-accent-100 text-accent-600 rounded-lg">Monthly</button>
            </div>
        </div>
        <div class="h-64">
            <canvas id="financialChart"></canvas>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white rounded-xl shadow-sm p-6 dashboard-card">
        <h3 class="text-lg font-semibold text-primary-900 mb-6">Recent Activities</h3>

        <div class="space-y-4">
            @forelse($recentActivities as $activity)
                <div class="flex items-start">
                    <!-- Icon -->
                    <div
                        class="w-8 h-8 bg-accent-100 text-accent-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                        <i class="fas fa-bolt text-xs"></i>
                    </div>

                    <!-- Activity Info -->
                    <div>
                        <p class="text-sm font-medium text-primary-900">{{ $activity->activity }}</p>
                        <p class="text-xs text-primary-500">
                            @if ($activity->causer)
                                {{ class_basename($activity->causer_type) }}: {{ $activity->causer->name ?? 'N/A' }}
                            @else
                                System
                            @endif
                        </p>
                        <p class="text-xs text-primary-400 mt-1">
                            {{ optional($activity->activity_date)->diffForHumans() ?? $activity->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">No recent activities found.</p>
            @endforelse
        </div>

        <a href="{{ route('admin.users.activities') }}"
            class="block text-center text-accent-600 hover:text-accent-700 text-sm font-medium mt-6">
            View All Activities
        </a>
    </div>
</div>

@php
    $settings = \App\Models\AdminSetting::select('currency')->first();
@endphp



@push('headScripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
@push('bodyScripts')
    <script>
    const ctx = document.getElementById('financialChart').getContext('2d');
    const chartData = @json($chartData);

    new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '{{ $settings->currency }} ' + (value / 1000) + 'K';
                        }
                    }
                }
            }
        }
    });
</script>
@endpush