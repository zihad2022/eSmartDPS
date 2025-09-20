<x-client.layout.app>
    @php
        $breadcrumbItems = [
            ['label' => 'Dashboard', 'url' => route('client.dashboard')],
            ['label' => 'Activities', 'url' => route('client.users.activities')],
        ];
        $pageTitle = 'User Activities';
    @endphp

    <x-slot:title>{{ $pageTitle }}</x-slot:title>
    <x-breadcrumb :items="$breadcrumbItems" />

    <div>
        @if (session('success') || session('error'))
            <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
        @endif

        <!-- Activities Table -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-primary-900">Recent Activities</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                User</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Activity</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                IP</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Browser</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                System</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                                Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($activities as $activity)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap flex items-center">
                                    <img src="{{ $activity->causer?->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($activity->causer?->name ?? 'Unknown') }}"
                                        class="w-10 h-10 rounded-full mr-3">
                                    <div>
                                        <div class="text-sm font-medium text-primary-900">
                                            {{ $activity->causer?->first_name . ' ' . $activity->causer?->last_name ?? 'Unknown User' }}
                                        </div>
                                        <div class="text-sm text-primary-500">
                                            {{ $activity->causer?->email ?? '-' }}
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-primary-900">{{ $activity->activity }}</td>
                                <td class="px-6 py-4 text-sm text-primary-600">{{ $activity->ip_address ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-primary-600">
                                    {{ $activity->browser }} {{ $activity->version }}
                                </td>
                                <td class="px-6 py-4 text-sm text-primary-600">
                                    {{ $activity->system }}
                                </td>
                                <td class="px-6 py-4 text-sm text-primary-600">
                                    {{ $activity->activity_date ? $activity->activity_date->format('M d, Y h:i A') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-sm text-gray-500">
                                    No activities found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-primary-600">
                        @if ($activities->total() > 0)
                            Showing {{ $activities->firstItem() }} to {{ $activities->lastItem() }} of
                            {{ $activities->total() }} results
                        @else
                            No results found.
                        @endif
                    </div>
                    <div class="flex space-x-2">
                        <x-pagination :paginator="$activities" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-client.layout.app>
