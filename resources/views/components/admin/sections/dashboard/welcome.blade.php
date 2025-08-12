@props(['admin'])
<div class="bg-gradient-to-r from-primary-900 to-primary-800 rounded-xl p-6 mb-6 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mt-10 -mr-10"></div>
    <div class="relative z-10">
        <h2 class="text-xl md:text-2xl font-bold mb-2">Welcome back, {{ $admin->name }}!</h2>
        <p class="text-primary-100 mb-4 text-sm md:text-base">Here's what's happening with DYDS savings
            management today.</p>

        <div class="flex flex-wrap gap-2 md:gap-4 mt-4">
            <a href="{{ route('admin.clients.create') }}"
                class="bg-accent-600 hover:bg-accent-700 px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm font-medium transition duration-300">
                New Client
            </a>
            <a href="{{ route('admin.packages.create') }}"
                class="bg-white/10 hover:bg-white/20 px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm font-medium transition duration-300">
                New Package
            </a>
        </div>
    </div>
</div>
