<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    {{-- Dynamic Title for each page --}}
    <title>{{ $title ?? 'Dashboard' }} - {{ $settings->site_name ?? config('app.name') }}</title>

    {{-- SEO Meta Tags --}}
    <meta name="description" content="{{ $settings->site_description ?? 'Welcome to our website.' }}" />
    <meta name="keywords" content="{{ $settings->site_keywords ?? 'web, app, services' }}" />

    {{-- Open Graph Meta Tags (for Facebook, LinkedIn, etc.) --}}
    <meta property="og:title" content="{{ $title ?? $settings->site_name }}" />
    <meta property="og:description" content="{{ $settings->site_description ?? '' }}" />
    <meta property="og:image"
        content="{{ $settings->graph_thumbnail ? $settings->graph_thumbnail_url : asset('default-og.jpg') }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />

    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $title ?? $settings->site_name }}" />
    <meta name="twitter:description" content="{{ $settings->site_description ?? '' }}" />
    <meta name="twitter:image"
        content="{{ $settings->graph_thumbnail ? $settings->graph_thumbnail_url : asset('default-og.jpg') }}" />

    {{-- Favicon --}}
    <link rel="icon" href="{{ $settings->favicon_url ? $settings->favicon_url : asset('favicon.ico') }}"
        type="image/x-icon" />

    {{-- Fonts & Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />

    {{-- Main Styles & Scripts (compiled with Vite) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js','resources/css/custom-styles.css'])
    
    {{-- Chart.js for Graphs --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="font-sans bg-gray-50 text-primary-900">
    <div class="flex h-screen bg-gray-100">
        
        {{-- Mobile Sidebar Overlay --}}
        <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>

        {{-- Sidebar --}}
        @include('admin.layout.partials.sidebar')

        {{-- Main Content Area --}}
        <main class="flex-1 md:ml-0 transition-all duration-300">

            {{-- Top Navigation --}}
            @include('admin.layout.partials.topbar')

            {{-- Page Content (Injected via $slot) --}}
            <div class="p-4 md:p-6">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            @include('admin.layout.partials.footer')
        </main>
    </div>

    {{-- Core Scripts --}}
    <script>
        // Sidebar Controls (For Mobile)
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

        // Dropdown Controls (Sidebar & Navigation)
        document.querySelectorAll('.dropdown-toggle').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const dropdown = document.getElementById(targetId);
                const chevron = this.querySelector('.fa-chevron-down');

                // Close all other dropdowns first
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    if (menu.id !== targetId) {
                        menu.classList.remove('active');
                        const otherChevron = document.querySelector(`[data-target="${menu.id}"] .fa-chevron-down`);
                        if (otherChevron) {
                            otherChevron.style.transform = 'rotate(0deg)';
                        }
                    }
                });

                // Toggle the clicked dropdown
                dropdown.classList.toggle('active');
                chevron.style.transform = dropdown.classList.contains('active') 
                    ? 'rotate(180deg)' 
                    : 'rotate(0deg)';
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

        // Close dropdowns if user clicks outside
        document.addEventListener('click', function() {
            notificationDropdown.classList.remove('active');
            profileDropdown.classList.remove('active');
        });
    </script>

    {{-- Extra Scripts (Injected using @push) --}}
    @stack('scripts')

</body>
</html>
