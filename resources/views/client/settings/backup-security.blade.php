<x-client.settings.layout>
    {{-- Display success or error flash message --}}
    @if (session('success') || session('error'))
        <x-flash-message 
            :type="session('success') ? 'success' : 'error'" 
            :title="session('success') ? 'Success' : 'Error'" 
            :message="session('success') ?? session('error')" 
        />
    @endif

    <x-slot name="title">Settings</x-slot>

    <div id="settings" class="settings-content bg-white rounded-xl shadow-sm p-6 space-y-10">

        {{-- ===============================
            Backup & Security Section
        =============================== --}}
        <div id="backup">
            <h3 class="text-lg font-semibold text-primary-900 mb-6">Backup & Security</h3>

            <form class="space-y-6" action="{{ route('client.settings.backup-security.update') }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Database Backup --}}
                <div>
                    <h4 class="text-md font-medium text-primary-900 mb-4">Database Backup</h4>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-4">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-sm font-medium text-primary-900">Last Backup</p>
                                <p class="text-sm text-primary-600">May 15, 2025 at 2:30 AM</p>
                            </div>
                            <button type="button" class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded-lg text-sm transition duration-300">
                                Backup Now
                            </button>
                        </div>

                        <x-form.checkbox 
                            name="auto_backup" 
                            label="Enable automatic daily backups" 
                            :checked="old('auto_backup', $settings->auto_backup)" 
                        />
                    </div>
                </div>

                {{-- Security Settings --}}
                <div>
                    <h4 class="text-md font-medium text-primary-900 mb-4">Security Settings</h4>
                    <div class="space-y-4">
                        <x-form.checkbox 
                            name="two_factor_auth" 
                            label="Enable two-factor authentication" 
                            :checked="old('two_factor_auth', $settings->two_factor_auth)" 
                        />

                        <x-form.checkbox 
                            name="session_timeout" 
                            label="Auto logout after 30 minutes of inactivity" 
                            :checked="old('session_timeout', $settings->session_timeout)" 
                        />

                        <x-form.checkbox 
                            name="login_notifications" 
                            label="Send email notifications for new logins" 
                            :checked="old('login_notifications', $settings->login_notifications)" 
                        />
                    </div>
                </div>

                {{-- Data Export --}}
                <div>
                    <h4 class="text-md font-medium text-primary-900 mb-4">Data Export</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <button type="button" class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-3 rounded-lg text-sm transition duration-300">
                            Export Members Data
                        </button>
                        <button type="button" class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-3 rounded-lg text-sm transition duration-300">
                            Export Payments Data
                        </button>
                        <button type="button" class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-3 rounded-lg text-sm transition duration-300">
                            Export Projects Data
                        </button>
                        <button type="button" class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-3 rounded-lg text-sm transition duration-300">
                            Export Ledger Data
                        </button>
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit" class="bg-accent-500 hover:bg-accent-600 text-white px-6 py-2 rounded-lg transition duration-300">
                    Save Security Settings
                </button>
            </form>
        </div>
    </div>
</x-client.settings.layout>
