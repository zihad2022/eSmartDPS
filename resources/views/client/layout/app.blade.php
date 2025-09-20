<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Laravel Vite (CSS + JS) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/custom-styles.css'])

    {{-- Dynamic Page Title --}}
    <title>{{ $title ?? 'Dashboard' }} - {{ $settings->site_name ?? config('app.name') }}</title>

    {{-- Fonts & Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="font-sans bg-gray-50 text-primary-900">
    <div class="flex h-screen bg-gray-100">

        {{-- Mobile Overlay (background when sidebar opens on small screens) --}}
        <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>

        {{-- Sidebar --}}
        @include('client.layout.partials.sidebar')

        {{-- Main Content --}}
        <main class="flex-1 md:ml-0 transition-all duration-300">

            {{-- Top Navigation Bar --}}
            @include('client.layout.partials.topbar')

            {{-- Page Content (dynamic) --}}
            @if (url()->current() === url('client/subscription/expired'))
                <div>
                    {{ $slot }}
                </div>
            @else
                <div class="p-4 md:p-6">
                    {{ $slot }}
                </div>
            @endif

            {{-- Footer --}}
            @include('client.layout.partials.footer')
        </main>

    </div>

    {{-- App Scripts --}}
    <script>
        // Sidebar toggle (mobile view)
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const mobileOverlay = document.getElementById('mobileOverlay');
        const closeSidebar = document.getElementById('closeSidebar');

        function openSidebar() {
            sidebar.classList.add('active');
            mobileOverlay.classList.remove('hidden');
        }

        function closeSidebarFunc() {
            sidebar.classList.remove('active');
            mobileOverlay.classList.add('hidden');
        }

        sidebarToggle.addEventListener('click', openSidebar);
        closeSidebar.addEventListener('click', closeSidebarFunc);
        mobileOverlay.addEventListener('click', closeSidebarFunc);

        // Dropdown toggle (sidebar menus)
        document.querySelectorAll('.dropdown-toggle').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const dropdown = document.getElementById(targetId);
                const chevron = this.querySelector('.fa-chevron-down');

                // Close all other dropdowns first
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    if (menu.id !== targetId) {
                        menu.classList.remove('active');
                        const otherChevron = document.querySelector(
                            `[data-target="${menu.id}"] .fa-chevron-down`);
                        if (otherChevron) {
                            otherChevron.style.transform = 'rotate(0deg)';
                        }
                    }
                });

                // Toggle current dropdown
                dropdown.classList.toggle('active');
                chevron.style.transform = dropdown.classList.contains('active') ? 'rotate(180deg)' :
                    'rotate(0deg)';
            });
        });

        // Notification dropdown
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');
        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('active');
            profileDropdown.classList.remove('active');
        });

        // Profile dropdown
        const profileBtn = document.getElementById('profileBtn');
        const profileDropdown = document.getElementById('profileDropdown');
        profileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('active');
            notificationDropdown.classList.remove('active');
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function() {
            notificationDropdown.classList.remove('active');
            profileDropdown.classList.remove('active');
        });

        // Financial Chart
        const ctx = document.getElementById('financialChart').getContext('2d');
        const financialChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                        label: 'Income',
                        data: [200000, 250000, 220000, 280000, 320000, 350000],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Expenses',
                        data: [80000, 95000, 70000, 110000, 130000, 120000],
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Loans',
                        data: [50000, 75000, 60000, 85000, 90000, 82000],
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
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
                            callback: value => '₹' + (value / 1000) + 'K'
                        }
                    }
                }
            }
        });

        // Loan Distribution Chart
        const loanCtx = document.getElementById('loanChart').getContext('2d');
        const loanChart = new Chart(loanCtx, {
            type: 'doughnut',
            data: {
                labels: ['Quick Loans', 'Personal Loans', 'Business Loans', 'Emergency Loans'],
                datasets: [{
                    data: [35, 25, 25, 15],
                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Member Growth Chart
        const memberCtx = document.getElementById('memberGrowthChart').getContext('2d');
        const memberGrowthChart = new Chart(memberCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'New Members',
                    data: [5, 8, 6, 10, 7, 9],
                    backgroundColor: '#10b981',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 2
                        }
                    }
                }
            }
        });
    </script>

    {{-- Stack for extra scripts (pushed from child views) --}}
    @stack('scripts')
</body>

</html>
