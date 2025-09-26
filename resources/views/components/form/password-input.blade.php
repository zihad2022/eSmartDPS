@props([
    'label' => '',
    'name',
    'value' => '',
    'placeholder' => '',
    'required' => false,
])

<div x-data="{ show: false }" class="w-full">
    <!-- Label -->
    <label for="{{ $name }}" class="block text-sm font-medium text-primary-700 mb-2">
        {{ $label }}
        @if($required)<span class="text-red-500">*</span>@endif
    </label>

    <!-- Input + Toggle -->
    <div class="relative">
        <input 
            id="{{ $name }}"
            name="{{ $name }}"
            x-bind:type="show ? 'text' : 'password'"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent-500 pr-10"
        />

        <!-- Toggle button -->
        <button type="button" 
            @click="show = !show" 
            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700">
            
            <!-- Show icon -->
            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>

            <!-- Hide icon -->
            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 012.39-3.68M6.343 6.343A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.963 9.963 0 01-4.132 5.411M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
            </svg>
        </button>
    </div>

    <!-- Validation error -->
    @error($name)
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>
