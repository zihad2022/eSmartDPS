<x-admin.settings.layout>
    @if (session('success') || session('error'))
        <x-flash-message :type="session('success') ? 'success' : 'error'" :title="session('success') ? 'Success' : 'Error'" :message="session('success') ?? session('error')" />
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-primary-900">Database Backups</h3>
                    <p class="mt-1 text-sm text-primary-500">Compressed logical backups are stored privately and retained up to the latest 20 files.</p>
                </div>
                <form method="POST" action="{{ route('admin.settings.backups.store') }}">
                    @csrf
                    <button type="submit" class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded-lg text-sm transition">
                        <i class="fas fa-database mr-2"></i>Create Backup
                    </button>
                </form>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-primary-500">File</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-primary-500">Created</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-primary-500">Size</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-primary-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($backups as $backup)
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-primary-900">{{ $backup['name'] }}</td>
                                <td class="px-4 py-3 text-sm text-primary-600">{{ \Carbon\Carbon::createFromTimestamp($backup['modified_at'])->format('M d, Y h:i A') }}</td>
                                <td class="px-4 py-3 text-sm text-primary-600">{{ number_format($backup['size'] / 1024, 1) }} KB</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('admin.settings.backups.download', $backup['name']) }}" class="text-accent-600 hover:text-accent-800" title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.settings.backups.destroy', $backup['name']) }}" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="delete-btn text-red-600 hover:text-red-800" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-primary-500">No database backups have been created.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.backup.update') }}" class="bg-white rounded-xl shadow-sm p-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <h3 class="text-lg font-semibold text-primary-900">Backup Schedule & Session Security</h3>
                <p class="mt-1 text-sm text-primary-500">Automatic backups require Laravel's scheduler/cron to be running.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="backup_frequency" class="block text-sm font-medium text-primary-700 mb-2">Backup Frequency</label>
                    <select id="backup_frequency" name="backup_frequency" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-accent-500">
                        @foreach (['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('backup_frequency', $settings->backup_frequency) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="session_timeout_minutes" class="block text-sm font-medium text-primary-700 mb-2">Admin Session Timeout (minutes)</label>
                    <input id="session_timeout_minutes" name="session_timeout_minutes" type="number" min="5" max="1440" required
                        value="{{ old('session_timeout_minutes', $settings->session_timeout_minutes ?? 30) }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-accent-500">
                </div>
            </div>

            <label class="flex items-center gap-3 rounded-lg bg-gray-50 p-4">
                <input type="hidden" name="backup_enabled" value="0">
                <input type="checkbox" name="backup_enabled" value="1" @checked(old('backup_enabled', $settings->backup_enabled))
                    class="h-4 w-4 rounded border-gray-300 text-accent-500 focus:ring-accent-500">
                <span>
                    <span class="block text-sm font-medium text-primary-900">Enable automatic backups</span>
                    <span class="block text-xs text-primary-500">The hourly scheduler checks whether the selected frequency is due.</span>
                </span>
            </label>

            @if ($settings->last_backup_at)
                <p class="text-sm text-primary-600">
                    Last successful backup: <strong>{{ $settings->last_backup_at->format('M d, Y h:i A') }}</strong>
                </p>
            @endif

            <div class="flex justify-end">
                <button type="submit" class="bg-accent-500 hover:bg-accent-600 text-white px-6 py-2 rounded-lg text-sm transition">
                    Save Backup & Security Settings
                </button>
            </div>
        </form>
    </div>
</x-admin.settings.layout>
