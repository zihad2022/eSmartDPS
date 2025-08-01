{{-- @props([
    'label' => '',
    'name',
    'type' => 'text',
    'required' => false,
    'placeholder' => '',
    'value' => '',
    'editing' => false,
    'previewUrl' => null,
    'accept' => '',
])

<div class="w-full">
    <label for="{{ $name }}" class="block text-sm font-medium text-primary-700 mb-2">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <input id="{{ $name }}" type="{{ $type }}" name="{{ $name }}"
        value="{{ $type !== 'file' ? old($name, $value) : '' }}" placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }} @if ($type === 'file' && $accept) accept="{{ $accept }}" @endif
        {{ $attributes->merge(['class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent-500']) }} />

    @if ($type === 'file' && $editing && $previewUrl)
        <img src="{{ $previewUrl }}" alt="{{ $label }}" class="mt-2 w-48 object-contain rounded">
    @endif

    @error($name)
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div> --}}


@props([
    'label' => '',
    'name',
    'type' => 'text',
    'required' => false,
    'placeholder' => '',
    'value' => '',
    'editing' => false,
    'previewUrl' => null,
    'accept' => '',
    'disabled' => false, // New prop
])

<div class="w-full">
    <label for="{{ $name }}" class="block text-sm font-medium text-primary-700 mb-2">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    @if ($disabled)
        <div
            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-500 text-sm cursor-not-allowed">
            {{ old($name, $value) }}
        </div>
        <input type="hidden" name="{{ $name }}" value="{{ old($name, $value) }}">
    @else
        <input id="{{ $name }}" type="{{ $type }}" name="{{ $name }}"
            value="{{ $type !== 'file' ? old($name, $value) : '' }}" placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }} @if ($type === 'file' && $accept) accept="{{ $accept }}" @endif
            {{ $attributes->merge(['class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent-500']) }} />
    @endif

    @if ($type === 'file' && $editing && $previewUrl)
        <img src="{{ $previewUrl }}" alt="{{ $label }}" class="mt-2 w-48 object-contain rounded">
    @endif

    @error($name)
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>
