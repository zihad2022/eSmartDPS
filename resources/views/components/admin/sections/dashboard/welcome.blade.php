@props(['admin'])

{{-- ================================
     Welcome Banner (Admin Dashboard)
================================ --}}
<div class="bg-gradient-to-r from-primary-900 to-primary-800 rounded-xl p-6 mb-6 text-white relative overflow-hidden flex flex-col md:flex-row items-center justify-between">

    {{-- Decorative Circle (Background Accent) --}}
    <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mt-10 -mr-10"></div>

    {{-- ===========================
         Left Content (Greeting + Actions)
    ============================ --}}
    <div class="relative z-10 flex-1 mb-6 md:mb-0">

        {{-- Greeting --}}
        <h2 class="text-xl md:text-2xl font-bold mb-2">Welcome back, {{ $admin->name }}!</h2>

        {{-- Quick Action Buttons --}}
        <div class="flex flex-wrap gap-2 md:gap-4 mt-4">
            {{-- Add New Client --}}
            <a href="{{ route('admin.clients.create') }}"
               class="bg-accent-600 hover:bg-accent-700 px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm font-medium transition duration-300">
                New Client
            </a>

            {{-- Add New Package --}}
            <a href="{{ route('admin.packages.create') }}"
               class="bg-white/10 hover:bg-white/20 px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm font-medium transition duration-300">
                New Package
            </a>
        </div>
    </div>

    {{-- ===========================
         Right Content (Digital Clock)
    ============================ --}}
    <div class="flex flex-col items-center justify-center text-center">
        {{-- Current Time --}}
        <div id="digital-time" class="text-3xl md:text-4xl font-bold text-white"></div>
        
        {{-- Current Day --}}
        <div id="day-name" class="text-lg md:text-xl text-white/80 mt-1"></div>
        
        {{-- Full Date --}}
        <div id="date-info" class="text-sm md:text-base text-white/70 mt-1"></div>
    </div>
</div>

{{-- ===========================
     JavaScript: Digital Clock
=========================== --}}
<script>
function updateClock() {
    const now = new Date();
    const pad = (n) => n.toString().padStart(2, '0');

    // 1. Convert hours to 12-hour format
    let hours = now.getHours();
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12 || 12;

    // 2. Update digital time
    document.getElementById('digital-time').textContent =
        `${pad(hours)}:${pad(now.getMinutes())}:${pad(now.getSeconds())} ${ampm}`;

    // 3. Update day name
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    document.getElementById('day-name').textContent = days[now.getDay()];

    // 4. Update full date (Month + Day + Year)
    const months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    document.getElementById('date-info').textContent = `${months[now.getMonth()]} ${now.getDate()}, ${now.getFullYear()}`;
}

// Initialize clock + refresh every second
updateClock();
setInterval(updateClock, 1000);
</script>
