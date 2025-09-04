@props([
    'name',
    'label' => null,
    'checked' => false,        // for single checkbox
    'checkboxValue' => 1,      // value for single checkbox
    'options' => [],           // for multiple checkboxes [value => label]
    'selected' => [],          // selected values (for multiple checkboxes)
])

<div class="space-y-2">
    {{-- Label for group --}}
    @if ($label && !empty($options))
        <label class="block text-sm font-medium text-primary-700 mb-2">{{ $label }}</label>
    @endif

    {{-- Case 1: Multiple checkboxes --}}
    @if (!empty($options))
        @foreach ($options as $value => $optionLabel)
            <div class="flex items-center">
                <input 
                    type="checkbox" 
                    id="{{ $name.'_'.$value }}" 
                    name="{{ $name }}[]" 
                    value="{{ $value }}"
                    {{ in_array($value, (array) $selected) ? 'checked' : '' }}
                    class="w-4 h-4 text-accent-500 bg-gray-100 border-gray-300 rounded focus:ring-accent-500"
                >
                <label for="{{ $name.'_'.$value }}" class="ml-2 text-sm text-primary-700">
                    {{ $optionLabel }}
                </label>
            </div>
        @endforeach

    {{-- Case 2: Single checkbox --}}
    @else
        <div class="flex items-center">
            <!-- Always send 0 if not checked -->
            <input type="hidden" name="{{ $name }}" value="0">
        
            <input type="checkbox"
                id="{{ $name }}"
                name="{{ $name }}"
                value="1"
                {{ $checked ? 'checked' : '' }}
                class="w-4 h-4 text-accent-500 bg-gray-100 border-gray-300 rounded focus:ring-accent-500">
        
            @if ($label)
                <label for="{{ $name }}" class="ml-2 text-sm text-primary-700">
                    {{ $label }}
                </label>
            @endif
        </div>
        
    @endif
</div>
