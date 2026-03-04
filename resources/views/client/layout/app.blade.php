<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/custom-styles.css'])

    <title>{{ $title ?? 'Dashboard' }} - {{ $settings->site_name ?? config('app.name') }}</title>

    {{-- Fonts & Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Vendor Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('assets/js/countdown.js') }}"></script>
</head>

<body class="font-sans bg-gray-50 text-primary-900">
    <div class="flex h-screen bg-gray-100 overflow-hidden">

        {{-- Mobile Sidebar Overlay --}}
        <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>

        {{-- Sidebar --}}
        @include('client.layout.partials.sidebar')

        {{-- Main Content --}}
        <main class="flex-1 transition-all duration-300 overflow-y-auto w-full">

            @include('client.layout.partials.topbar')

            {{-- Payment Banner (Dynamic) --}}
            @if (!request()->is('client/checkout*'))
                @php
                    $client = Auth::guard('client')->user();
                    $latestUnpaidInvoice = $client?->invoices()->where('status', \App\Enums\InvoiceStatus::UNPAID)->latest()->first();
                @endphp

                @if ($latestUnpaidInvoice)
                    <x-client.banners.payment-required-banner />
                @endif
            @endif

            {{-- Page Content --}}
            <div class="{{ url()->current() === url('client/subscription/expired') ? '' : 'p-4 md:p-6' }}">
                {{ $slot }}
            </div>

            @include('client.layout.partials.footer')

        </main>

    </div>

    {{-- Layout Scripts --}}
    <script>
        /* -------------------------------
         * Sidebar Handling (Mobile)
         * ------------------------------ */
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const mobileOverlay = document.getElementById('mobileOverlay');
        const closeSidebar = document.getElementById('closeSidebar');

        function toggleSidebar(show) {
            sidebar.classList.toggle('active', show);
            mobileOverlay.classList.toggle('hidden', !show);
        }

        sidebarToggle?.addEventListener('click', () => toggleSidebar(true));
        closeSidebar?.addEventListener('click', () => toggleSidebar(false));
        mobileOverlay?.addEventListener('click', () => toggleSidebar(false));

        /* -------------------------------
         * Sidebar Dropdown Menus
         * ------------------------------ */
        document.querySelectorAll('.dropdown-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = document.getElementById(btn.dataset.target);
                const rotationIcon = btn.querySelector('.fa-chevron-down');

                // Close all other dropdowns first
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    if (menu !== target) {
                        menu.classList.remove('active');
                        const icon = document.querySelector(`[data-target="${menu.id}"] .fa-chevron-down`);
                        if (icon) icon.style.transform = 'rotate(0deg)';
                    }
                });

                // Toggle current dropdown
                target.classList.toggle('active');
                rotationIcon.style.transform = target.classList.contains('active')
                    ? 'rotate(180deg)' : 'rotate(0deg)';
            });
        });

        /* -------------------------------
         * Notification & Profile Menus
         * ------------------------------ */
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const profileBtn = document.getElementById('profileBtn');
        const profileDropdown = document.getElementById('profileDropdown');

        notificationBtn?.addEventListener('click', e => {
            e.stopPropagation();
            notificationDropdown.classList.toggle('active');
            profileDropdown?.classList.remove('active');
        });

        profileBtn?.addEventListener('click', e => {
            e.stopPropagation();
            profileDropdown.classList.toggle('active');
            notificationDropdown?.classList.remove('active');
        });

        document.addEventListener('click', () => {
            notificationDropdown?.classList.remove('active');
            profileDropdown?.classList.remove('active');
        });

        /* -------------------------------
         * Charts Initialization
         * ------------------------------ */
        const renderChart = (id, config) => {
            const canvas = document.getElementById(id);
            if (canvas) {
                new Chart(canvas.getContext('2d'), config);
            }
        };

        // Financial Chart
        renderChart('financialChart', {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [
                    {
                        label: 'Income',
                        data: [200000, 250000, 220000, 280000, 320000, 350000],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Expenses',
                        data: [80000, 95000, 70000, 110000, 130000, 120000],
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Loans',
                        data: [50000, 75000, 60000, 85000, 90000, 82000],
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: value => '৳' + (value / 1000) + 'K' }
                    }
                }
            }
        });

        // Loan Chart
        renderChart('loanChart', {
            type: 'doughnut',
            data: {
                labels: ['Quick Loans', 'Personal Loans', 'Business Loans', 'Emergency Loans'],
                datasets: [{
                    data: [35, 25, 25, 15],
                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });

        // Member Growth Chart
        renderChart('memberGrowthChart', {
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
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 2 } }
                }
            }
        });
    </script>

    @stack('scripts')

</body>

</html>
