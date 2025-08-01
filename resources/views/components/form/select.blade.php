@props([
    'name' => '',
    'label' => '',
    'options' => [],
    'selected' => null,
    'required' => false,
    'isOptionLabel' => false,
    'optionLabel' => '',
])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-primary-700 mb-2">
        {{ $label }}
        @if ($required)
            <span class="text-red-600">*</span>
        @endif
    </label>

    <select name="{{ $name }}" id="{{ $name }}"
        {{ $attributes->merge([
            'class' =>
                'w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent-500',
        ]) }}
        @if ($required) required @endif>
        @if ($isOptionLabel)
            <option value="">-- {{ $optionLabel }} --</option>
        @endif
        @foreach ($options as $value => $optionLabel)
            <option value="{{ $value }}" {{ old($name, $selected) == $value ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    @error($name)
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>
