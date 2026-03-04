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
        const S=(o)=>o.classList.toggle('active'),H=(o)=>o.classList.toggle('hidden'); 
        sidebarToggle.onclick=()=>{S(sidebar);H(mobileOverlay)}; 
        closeSidebar.onclick=mobileOverlay.onclick=()=>{sidebar.classList.remove('active');mobileOverlay.classList.add('hidden')};

        document.querySelectorAll('.dropdown-toggle').forEach(b=>b.onclick=function(){let d=document.getElementById(this.dataset.target);document.querySelectorAll('.dropdown-menu').forEach(m=>m!=d&&m.classList.remove('active'));d.classList.toggle('active');this.querySelector('.fa-chevron-down').style.transform=d.classList.contains('active')?'rotate(180deg)':'rotate(0deg)'});

        notificationBtn.onclick=(e)=>{e.stopPropagation();S(notificationDropdown);profileDropdown.classList.remove('active')};
        profileBtn.onclick=(e)=>{e.stopPropagation();S(profileDropdown);notificationDropdown.classList.remove('active')};
        document.onclick=()=>{notificationDropdown.classList.remove('active');profileDropdown.classList.remove('active')};
    </script>

    {{-- Extra Scripts (Injected using @push) --}}
    @stack('scripts')
    @stack('bodyScripts')
</body>
</html>
