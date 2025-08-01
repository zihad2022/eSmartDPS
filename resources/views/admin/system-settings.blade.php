<x-admin.layout.app>
    <!-- Settings Content -->
    <div class="">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Settings Navigation -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4">Settings Categories</h3>
                    <nav class="space-y-2">
                        <button
                            class="settings-nav-btn active w-full text-left px-4 py-3 rounded-lg transition duration-300"
                            data-target="general">
                            <i class="fas fa-cog mr-3"></i>General Settings
                        </button>
                        <button class="settings-nav-btn w-full text-left px-4 py-3 rounded-lg transition duration-300"
                            data-target="shares">
                            <i class="fas fa-chart-pie mr-3"></i>Share Settings
                        </button>
                        <button class="settings-nav-btn w-full text-left px-4 py-3 rounded-lg transition duration-300"
                            data-target="payments">
                            <i class="fas fa-money-bill-wave mr-3"></i>Payment Settings
                        </button>
                        <button class="settings-nav-btn w-full text-left px-4 py-3 rounded-lg transition duration-300"
                            data-target="notifications">
                            <i class="fas fa-bell mr-3"></i>Notifications
                        </button>
                        <button class="settings-nav-btn w-full text-left px-4 py-3 rounded-lg transition duration-300"
                            data-target="backup">
                            <i class="fas fa-database mr-3"></i>Backup & Security
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Settings Content -->
            <div class="lg:col-span-2">
                <!-- General Settings -->
                @include('admin.settings.general')

                <!-- Share Settings -->
                @include('admin.settings.share')

                <!-- Payment Settings -->
                @include('admin.settings.payment')

                <!-- Notifications -->
                @include('admin.settings.notification')

                <!-- Backup & Security -->
                @include('admin.settings.backup-security')
            </div>
        </div>
    </div>
    <script>
        // Settings Navigation
        document.querySelectorAll('.settings-nav-btn').forEach(button => {
            button.addEventListener('click', function() {
                const target = this.getAttribute('data-target');

                // Remove active class from all buttons
                document.querySelectorAll('.settings-nav-btn').forEach(btn => {
                    btn.classList.remove('active', 'bg-accent-500', 'text-white');
                    btn.classList.add('text-primary-600', 'hover:bg-gray-50');
                });

                // Add active class to clicked button
                this.classList.add('active', 'bg-accent-500', 'text-white');
                this.classList.remove('text-primary-600', 'hover:bg-gray-50');

                // Hide all content sections
                document.querySelectorAll('.settings-content').forEach(content => {
                    content.classList.add('hidden');
                });

                // Show target content
                document.getElementById(target).classList.remove('hidden');
            });
        });

        // Initialize first tab as active
        document.querySelector('.settings-nav-btn.active').classList.add('bg-accent-500', 'text-white');
        document.querySelector('.settings-nav-btn.active').classList.remove('text-primary-600');
    </script>
</x-admin.layout.app>
