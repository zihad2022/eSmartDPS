{{-- <!-- resources/views/components/flash-message.blade.php -->
<div x-data="{ show: true }" x-show="show" x-transition
    class="fixed top-5 right-5 z-50 max-w-xs rounded-lg shadow-lg text-white"
    :class="{
        'bg-green-500': '{{ $type }}'
        === 'success',
        'bg-red-500': '{{ $type }}'
        === 'error',
    }"
    x-init="setTimeout(() => show = false, 4000)">
    <div class="flex items-center justify-between px-4 py-3">
        <div class="text-sm">
            <strong class="block text-base">{{ $title }}</strong>
            <span>{{ $message }}</span>
        </div>
        <button @click="show = false" class="ml-3 text-white hover:text-gray-200">
            &times;
        </button>
    </div>
</div> --}}

<!-- resources/views/components/flash-message.blade.php -->
{{-- <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-90" @keydown.escape.window="show = false"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6 text-center" @click.away="show = false">
        <!-- Icon -->
        <div class="text-4xl mb-4">
            @if ($type === 'success')
                <i class="fas fa-check-circle text-green-500"></i>
            @elseif ($type === 'error')
                <i class="fas fa-times-circle text-red-500"></i>
            @elseif ($type === 'warning')
                <i class="fas fa-exclamation-triangle text-yellow-500"></i>
            @else
                <i class="fas fa-info-circle text-blue-500"></i>
            @endif
        </div>

        <!-- Title -->
        <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $title }}</h3>

        <!-- Message -->
        <p class="text-sm text-gray-500 mb-6">{{ $message }}</p>

        <!-- Actions -->
        <div class="flex justify-center space-x-3">
            <button @click="show = false"
                class="px-4 py-2 text-sm rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                Close
            </button>
        </div>
    </div>
</div> --}}

<!-- resources/views/components/flash-message.blade.php -->
{{-- <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-2" x-init="setTimeout(() => show = false, 4000)"
    class="fixed top-5 right-5 z-50 w-full max-w-sm pointer-events-auto">
    <div
        class="flex items-start gap-3 rounded-lg shadow-md px-4 py-3 border 
        @if ($type === 'success') bg-green-50 border-green-200 text-green-700
        @elseif($type === 'error') bg-red-50 border-red-200 text-red-700
        @elseif($type === 'warning') bg-yellow-50 border-yellow-200 text-yellow-700
        @else bg-blue-50 border-blue-200 text-blue-700 @endif">
        <div class="mt-1">
            @if ($type === 'success')
                <i class="fas fa-check-circle text-green-500"></i>
            @elseif ($type === 'error')
                <i class="fas fa-times-circle text-red-500"></i>
            @elseif ($type === 'warning')
                <i class="fas fa-exclamation-triangle text-yellow-500"></i>
            @else
                <i class="fas fa-info-circle text-blue-500"></i>
            @endif
        </div>

        <div class="flex-1 text-sm">
            <strong class="block font-semibold">{{ $title }}</strong>
            <span class="block">{{ $message }}</span>
        </div>

        <button @click="show = false" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div> --}}

<!-- resources/views/components/flash-message.blade.php -->
{{-- <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="opacity-0 translate-x-20" x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition ease-in duration-300 transform" x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 translate-x-20" x-init="setTimeout(() => show = false, 4000)"
    class="fixed top-5 right-5 z-50 w-full max-w-sm pointer-events-auto">
    <div
        class="flex items-start gap-3 rounded-xl shadow-lg px-4 py-3 border 
        @if ($type === 'success') bg-green-50 border-green-200 text-green-700
        @elseif($type === 'error') bg-red-50 border-red-200 text-red-700
        @elseif($type === 'warning') bg-yellow-50 border-yellow-200 text-yellow-700
        @else bg-blue-50 border-blue-200 text-blue-700 @endif">
        <div class="mt-1">
            @if ($type === 'success')
                <i class="fas fa-check-circle text-green-500"></i>
            @elseif ($type === 'error')
                <i class="fas fa-times-circle text-red-500"></i>
            @elseif ($type === 'warning')
                <i class="fas fa-exclamation-triangle text-yellow-500"></i>
            @else
                <i class="fas fa-info-circle text-blue-500"></i>
            @endif
        </div>

        <div class="flex-1 text-sm">
            <strong class="block font-semibold">{{ $title }}</strong>
            <span class="block">{{ $message }}</span>
        </div>

        <button @click="show = false" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div> --}}

<div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="opacity-0 scale-95 translate-y-3"
    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200 transform"
    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
    x-transition:leave-end="opacity-0 scale-95 translate-y-3" x-init="setTimeout(() => show = false, 4000)"
    class="fixed top-5 right-5 z-50 w-full max-w-sm pointer-events-auto">

    <div
        class="flex items-center gap-4 p-4 rounded-xl shadow-xl border backdrop-blur-sm 
        @if ($type === 'success') bg-green-50 border-green-200 text-green-700
        @elseif($type === 'error') bg-red-50 border-red-200 text-red-700
        @elseif($type === 'warning') bg-yellow-50 border-yellow-200 text-yellow-700
        @else bg-blue-50 border-blue-200 text-blue-700 @endif">

        <!-- Icon Section -->
        <div class="flex-shrink-0">
            <div
                class="w-10 h-10 flex items-center justify-center rounded-full 
                @if ($type === 'success') bg-green-100 
                @elseif($type === 'error') bg-red-100 
                @elseif($type === 'warning') bg-yellow-100 
                @else bg-blue-100 @endif">
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

        <!-- Content -->
        <div class="flex-1">
            <p class="text-sm font-semibold">{{ $title }}</p>
            <p class="text-sm">{{ $message }}</p>
        </div>

        <!-- Close Button -->
        <button @click="show = false"
            class="p-2 px-3.5 rounded-full text-gray-400 hover:bg-gray-200 hover:text-gray-700 transition">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
