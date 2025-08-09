{{-- 
|--------------------------------------------------------------------------
| Toast Notification Component
|--------------------------------------------------------------------------
| This reusable component displays temporary toast notifications in the top-right corner.
| It uses Alpine.js for:
| - Show/Hide state management
| - Entry & exit animations
| - Auto-hide after a set time (default: 4 seconds)
| 
| PARAMETERS:
|   $type    => Notification type: 'success', 'error', 'warning', or 'info'
|   $title   => Short notification title
|   $message => Detailed notification message
|
| USAGE:
|   <x-toast type="success" title="Saved!" message="Your changes have been saved." />
| 
| Author: Zihadul Islam - [Date]
| Purpose: Future-friendly and reusable notification system
--}}

<div x-data="{ show: true }" {{-- Alpine state: Controls visibility --}} x-show="show" {{-- Show toast only if 'show' is true --}} {{-- Transition: Fade + scale with smooth motion --}}
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="opacity-0 scale-95 translate-y-3"
    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200 transform"
    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
    x-transition:leave-end="opacity-0 scale-95 translate-y-3" {{-- Auto-hide after 4 seconds --}} x-init="setTimeout(() => show = false, 4000)"
    class="fixed top-5 right-5 z-50 w-full max-w-sm pointer-events-auto">
    <div
        class="flex items-center gap-4 p-4 rounded-xl shadow-xl border backdrop-blur-sm 
            {{-- Apply background & text color based on notification type --}}
            @if ($type === 'success') bg-green-50 border-green-200 text-green-700
            @elseif($type === 'error') bg-red-50 border-red-200 text-red-700
            @elseif($type === 'warning') bg-yellow-50 border-yellow-200 text-yellow-700
            @else bg-blue-50 border-blue-200 text-blue-700 @endif">
        {{-- ICON SECTION --}}
        <div class="flex-shrink-0">
            <div
                class="w-10 h-10 flex items-center justify-center rounded-full 
                    @if ($type === 'success') bg-green-100 
                    @elseif($type === 'error') bg-red-100 
                    @elseif($type === 'warning') bg-yellow-100 
                    @else bg-blue-100 @endif">
                {{-- Select icon based on type --}}
                @if ($type === 'success')
                    <i class="fas fa-check text-green-600 text-lg"></i>
                @elseif ($type === 'error')
                    <i class="fas fa-times text-red-600 text-lg"></i>
                @elseif ($type === 'warning')
                    <i class="fas fa-exclamation text-yellow-600 text-lg"></i>
                @else
                    <i class="fas fa-info text-blue-600 text-lg"></i>
                @endif
            </div>
        </div>

        {{-- TEXT CONTENT --}}
        <div class="flex-1">
            <p class="text-sm font-semibold">{{ $title }}</p>
            <p class="text-sm">{{ $message }}</p>
        </div>

        {{-- CLOSE BUTTON --}}
        <button @click="show = false" {{-- Hide toast when clicked --}}
            class="p-2 px-3.5 rounded-full text-gray-400 hover:bg-gray-200 hover:text-gray-700 transition">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
