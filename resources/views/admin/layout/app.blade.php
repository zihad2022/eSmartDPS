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
    
    {{-- Head Scripts --}}
    @stack('headScripts')
</head>

<body class="font-sans bg-gray-50 text-primary-900">
    <div class="flex h-screen bg-gray-100 overflow-hidden">
        
        {{-- Mobile Sidebar Overlay --}}
        <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>

        {{-- Sidebar --}}
        @include('admin.layout.partials.sidebar')

        {{-- Main Content Area --}}
        <main class="flex-1 md:ml-0 transition-all duration-300 overflow-y-auto w-full">

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
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const closeSidebar = document.getElementById('closeSidebar');
            const mobileOverlay = document.getElementById('mobileOverlay');
            const notificationBtn = document.getElementById('notificationBtn');
            const notificationDropdown = document.getElementById('notificationDropdown');
            const profileBtn = document.getElementById('profileBtn');
            const profileDropdown = document.getElementById('profileDropdown');

            const closeMobileSidebar = () => {
                sidebar?.classList.remove('active');
                mobileOverlay?.classList.add('hidden');
            };

            sidebarToggle?.addEventListener('click', () => {
                sidebar?.classList.toggle('active');
                mobileOverlay?.classList.toggle('hidden');
            });
            closeSidebar?.addEventListener('click', closeMobileSidebar);
            mobileOverlay?.addEventListener('click', closeMobileSidebar);

            document.querySelectorAll('.dropdown-toggle').forEach((button) => {
                button.addEventListener('click', () => {
                    const menu = document.getElementById(button.dataset.target);
                    if (!menu) return;

                    document.querySelectorAll('.dropdown-menu').forEach((item) => {
                        if (item !== menu) item.classList.remove('active');
                    });

                    menu.classList.toggle('active');
                    button.setAttribute('aria-expanded', menu.classList.contains('active') ? 'true' : 'false');
                    const chevron = button.querySelector('.fa-chevron-down');
                    if (chevron) {
                        chevron.style.transform = menu.classList.contains('active') ? 'rotate(180deg)' : 'rotate(0deg)';
                    }
                });
            });

            notificationBtn?.addEventListener('click', (event) => {
                event.stopPropagation();
                notificationDropdown?.classList.toggle('active');
                profileDropdown?.classList.remove('active');
            });

            profileBtn?.addEventListener('click', (event) => {
                event.stopPropagation();
                profileDropdown?.classList.toggle('active');
                notificationDropdown?.classList.remove('active');
            });

            document.addEventListener('click', () => {
                notificationDropdown?.classList.remove('active');
                profileDropdown?.classList.remove('active');
            });
        });
    </script>

    {{-- Global Confirmation Modal --}}
    <x-confirm-modal />

    {{-- Extra Scripts (Injected using @push) --}}
    @stack('scripts')
    @stack('bodyScripts')
</body>
</html>
