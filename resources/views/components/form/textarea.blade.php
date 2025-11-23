@props([
    'label' => '',
    'name',
    'required' => false,
    'placeholder' => '',
    'value' => '',
])

<div class="w-full">
      <label for="{{ $name }}" class="block text-sm font-medium text-primary-700 mb-2">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <textarea id="{{ $name }}" name="{{ $name }}" placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'w-full h-24 px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent-500']) }}>{{ old($name, $value) }}</textarea>

    @error($name)
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>
