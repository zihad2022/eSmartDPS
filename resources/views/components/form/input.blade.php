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

@php
    $isImagePreview = $previewUrl && \Illuminate\Support\Str::of(parse_url($previewUrl, PHP_URL_PATH) ?: '')
        ->lower()
        ->endsWith(['.jpg', '.jpeg', '.png', '.gif', '.webp', '.bmp', '.svg']);
@endphp

<div class="w-full">
    <label for="{{ $name }}" class="block text-sm font-medium text-primary-700 mb-2">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    @if ($disabled)
        <div class="w-full px-4 py-2 border border-gray-200 bg-gray-50 rounded-lg text-sm text-gray-700">
            {{ $value ?: '-' }}
        </div>
    @elseif ($type === 'file')
        <input
            id="{{ $name }}"
            type="file"
            name="{{ $name }}"
            {{ $required ? 'required' : '' }}
            @if ($accept) accept="{{ $accept }}" @endif
            {{ $attributes->merge([
                'class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent-500'
            ]) }}
            onchange="window.previewAdminFile(event, '{{ $name }}-preview', '{{ $name }}-filename')"
        />

        <div class="mt-2 space-y-2">
            <img id="{{ $name }}-preview"
                src="{{ $isImagePreview ? $previewUrl : '' }}"
                alt="{{ $label }}"
                class="w-48 max-h-48 object-contain rounded {{ $isImagePreview ? '' : 'hidden' }}">
            <p id="{{ $name }}-filename" class="text-xs text-primary-500"></p>
            @if ($previewUrl)
                <a href="{{ $previewUrl }}" target="_blank" rel="noopener" class="inline-flex items-center text-xs text-accent-600 hover:underline">
                    <i class="fas fa-external-link-alt mr-1"></i>View current file
                </a>
            @endif
        </div>

        @once
            <script>
                window.previewAdminFile = function (event, previewId, filenameId) {
                    const file = event.target.files && event.target.files[0];
                    const preview = document.getElementById(previewId);
                    const filename = document.getElementById(filenameId);

                    if (!file) {
                        filename.textContent = '';
                        return;
                    }

                    filename.textContent = file.name;

                    if (!file.type.startsWith('image/')) {
                        preview.src = '';
                        preview.classList.add('hidden');
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function (loadEvent) {
                        preview.src = loadEvent.target.result;
                        preview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                };
            </script>
        @endonce
    @else
        <input
            id="{{ $name }}"
            type="{{ $type }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge([
                'class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent-500'
            ]) }}
        />
    @endif

    @error($name)
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>
