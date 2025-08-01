<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Dashboard - DYDS Savings Software</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .sidebar-link {
            transition: all 0.3s ease;
        }

        .sidebar-link:hover {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .sidebar-link.active {
            background-color: #10b981;
            color: white;
        }

        .dashboard-card {
            transition: all 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .dropdown-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .dropdown-menu.active {
            max-height: 300px;
        }

        .notification-dropdown,
        .profile-dropdown {
            transform: translateY(-10px);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .notification-dropdown.active,
        .profile-dropdown.active {
            transform: translateY(0);
            opacity: 1;
            visibility: visible;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.active {
                transform: translateX(0);
            }
        }

        .pulse-dot {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .progress-bar {
            transition: width 1s ease-in-out;
        }
    </style>
</head>

<body class="font-sans bg-gray-50 text-primary-900">
    <div class="flex h-screen bg-gray-100">
        <!-- Mobile Overlay -->
        <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>

        <!-- Sidebar -->
        @include('client.layout.partials.sidebar')

        <!-- Main Content -->
        <main class="flex-1 md:ml-0 transition-all duration-300">
            <!-- Top Navigation -->
            @include('client.layout.partials.topbar')

            <!-- Dashboard Content -->
            <div class="p-4 md:p-6">
                {{ $slot }}
            </div>
            <!-- Footer -->
            @include('client.layout.partials.footer')
        </main>
    </div>

    <script>
        // Mobile Sidebar Toggle
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

        // Dropdown Toggle
        document.querySelectorAll('.dropdown-toggle').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const dropdown = document.getElementById(targetId);
                const chevron = this.querySelector('.fa-chevron-down');

                // Close other dropdowns
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

        // Notification Dropdown
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');

        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('active');
            profileDropdown.classList.remove('active');
        });

        // Profile Dropdown
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
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₹' + (value / 1000) + 'K';
                            }
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
                    backgroundColor: [
                        '#10b981',
                        '#3b82f6',
                        '#f59e0b',
                        '#ef4444'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
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
</body>

</html>
