@props([
    'label' => '',
    'name',
    'type' => 'text',
    'required' => false,
    'placeholder' => '',
    'value' => '52',
    'editing' => false,
    'previewUrl' => null,
    'accept' => '',
    'disabled' => false, // Control disabled state
])

<div class="w-full">
    {{-- Field label --}}
    <label for="{{ $name }}" class="block text-sm font-medium text-primary-700 mb-2">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    @if ($disabled)
        {{-- Show as plain text (not an input) so it won't submit --}}
        <div class="w-full px-4 py-2 border border-gray-200 bg-gray-50 rounded-lg text-sm text-gray-700">
            {{ $value ?: '-' }}
        </div>
    @else
        {{-- Normal input field --}}
        <input 
            id="{{ $name }}" 
            type="{{ $type }}" 
            name="{{ $name }}"
            value="{{ $type !== 'file' ? old($name, $value) : '' }}" 
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }} 
            @if ($type === 'file' && $accept) accept="{{ $accept }}" @endif
            {{ $attributes->merge([
                'class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg text-sm 
                            focus:outline-none focus:ring-2 focus:ring-accent-500'
            ]) }}
        />
    @endif

    {{-- File preview --}}
    @if ($type === 'file' && $editing && $previewUrl)
        <img src="{{ $previewUrl }}" alt="{{ $label }}" class="mt-2 w-48 object-contain rounded">
    @endif

    {{-- Validation error --}}
    @error($name)
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>
