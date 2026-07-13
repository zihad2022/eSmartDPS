@props(['id', 'title', 'icon' => 'fas fa-users', 'items' => [], 'active' => false])

@php
    $admin = auth('admin')->user();
    $visibleItems = collect($items)->filter(
        fn (array $item): bool => empty($item['permission']) || ($admin && $admin->can($item['permission']))
    );
@endphp

@if ($visibleItems->isNotEmpty())
    <div>
        <button type="button"
            class="sidebar-link dropdown-toggle flex items-center justify-between w-full px-3 py-2 rounded-lg {{ $active ? 'active' : '' }}"
            data-target="{{ $id }}" aria-controls="{{ $id }}" aria-expanded="{{ $active ? 'true' : 'false' }}">
            <div class="flex items-center space-x-3">
                <i class="{{ $icon }} w-5 text-center"></i>
                <span>{{ $title }}</span>
            </div>
            <i class="fas fa-chevron-down text-xs transition-transform"></i>
        </button>
        <div id="{{ $id }}" class="dropdown-menu ml-8 mt-1 space-y-1 {{ $active ? 'active' : '' }}">
            @foreach ($visibleItems as $item)
                <x-sidebar-item :url="$item['url']" :label="$item['label']" />
            @endforeach
        </div>
    </div>
@endif
