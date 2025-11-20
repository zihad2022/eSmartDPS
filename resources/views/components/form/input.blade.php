@props([
    'label' => '',
    'name',
    'type' => 'text',
    'required' => false,
    'placeholder' => '',
    'value' => null,
    'editing' => false,
    'previewUrl' => null,
    'accept' => '',
    'disabled' => false,
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
        {{-- Show as plain text --}}
        <div class="w-full px-4 py-2 border border-gray-200 bg-gray-50 rounded-lg text-sm text-gray-700">
            {{ $value ?: '-' }}
        </div>
    @else
        @if ($type === 'file')
            <input 
                id="{{ $name }}" 
                type="file" 
                name="{{ $name }}"
                {{ $required ? 'required' : '' }} 
                @if ($accept) accept="{{ $accept }}" @endif
                {{ $attributes->merge([
                    'class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg text-sm 
                                focus:outline-none focus:ring-2 focus:ring-accent-500'
                ]) }}
                onchange="previewFile(event, '{{ $name }}-preview')"
            />

            {{-- File preview container --}}
            <div class="mt-2">
                <img 
                    id="{{ $name }}-preview" 
                    src="{{ $previewUrl ?? '' }}" 
                    alt="{{ $label }}" 
                    class="w-48 object-contain rounded {{ $previewUrl ? '' : 'hidden' }}"
                >
            </div>

            <script>
                function previewFile(event, previewId) {
                    const input = event.target;
                    const preview = document.getElementById(previewId);

                    if (input.files && input.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.src = e.target.result;
                            preview.classList.remove('hidden');
                        }
                        reader.readAsDataURL(input.files[0]);
                    }
                }
            </script>
        @else
            <input 
                id="{{ $name }}" 
                type="{{ $type }}" 
                name="{{ $name }}"
                value="{{ old($name, $value) }}" 
                placeholder="{{ $placeholder }}"
                {{ $required ? 'required' : '' }}
                {{ $attributes->merge([
                    'class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg text-sm 
                                focus:outline-none focus:ring-2 focus:ring-accent-500'
                ]) }}
            />
        @endif
    @endif

    {{-- Validation error --}}
    @error($name)
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>
