{{-- resources/views/components/display/field.blade.php --}}
@props(['label', 'value'])

<div>
    <p class="text-xs text-gray-500">{{ $label }}</p>
    <p class="text-sm font-medium text-gray-900">{{ $value }}</p>
</div>
