<x-admin.settings.layout>
    <div id="backup" class="settings-content bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-primary-900 mb-6">Backup & Security</h3>

        <div class="space-y-6">
            <div>
                <h4 class="text-md font-medium text-primary-900 mb-4">Database Backup</h4>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm font-medium text-primary-900">Last Backup</p>
                            <p class="text-sm text-primary-600">May 15, 2025 at 2:30 AM</p>
                        </div>
                        <button
                            class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded-lg text-sm transition duration-300">
                            <i class="fas fa-download mr-2"></i>Backup Now
                        </button>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="autoBackup" checked
                            class="w-4 h-4 text-accent-500 bg-gray-100 border-gray-300 rounded focus:ring-accent-500">
                        <label for="autoBackup" class="ml-2 text-sm text-primary-700">Enable automatic
                            daily backups</label>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-md font-medium text-primary-900 mb-4">Security Settings</h4>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="twoFactor"
                            class="w-4 h-4 text-accent-500 bg-gray-100 border-gray-300 rounded focus:ring-accent-500">
                        <label for="twoFactor" class="ml-2 text-sm text-primary-700">Enable two-factor
                            authentication</label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="sessionTimeout" checked
                            class="w-4 h-4 text-accent-500 bg-gray-100 border-gray-300 rounded focus:ring-accent-500">
                        <label for="sessionTimeout" class="ml-2 text-sm text-primary-700">Auto logout
                            after 30 minutes of inactivity</label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="loginNotifications" checked
                            class="w-4 h-4 text-accent-500 bg-gray-100 border-gray-300 rounded focus:ring-accent-500">
                        <label for="loginNotifications" class="ml-2 text-sm text-primary-700">Send email
                            notifications for new logins</label>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-md font-medium text-primary-900 mb-4">Data Export</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <button
                        class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-3 rounded-lg text-sm transition duration-300">
                        <i class="fas fa-users mr-2"></i>Export Members Data
                    </button>
                    <button
                        class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-3 rounded-lg text-sm transition duration-300">
                        <i class="fas fa-money-bill-wave mr-2"></i>Export Payments Data
                    </button>
                    <button
                        class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-3 rounded-lg text-sm transition duration-300">
                        <i class="fas fa-project-diagram mr-2"></i>Export Projects Data
                    </button>
                    <button
                        class="bg-gray-100 hover:bg-gray-200 text-primary-700 px-4 py-3 rounded-lg text-sm transition duration-300">
                        <i class="fas fa-book mr-2"></i>Export Ledger Data
                    </button>
                </div>
            </div>

            <button type="submit"
                class="bg-accent-500 hover:bg-accent-600 text-white px-6 py-2 rounded-lg transition duration-300">
                Save Security Settings
            </button>
        </div>
    </div>
</x-admin.settings.layout>
