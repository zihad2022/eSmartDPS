@props(['id', 'title', 'icon' => 'fas fa-users', 'items' => [], 'active' => false])

<div>
    <button
        class="sidebar-link dropdown-toggle flex items-center justify-between w-full px-3 py-2 rounded-lg {{ $active ? 'active' : '' }}"
        data-target="{{ $id }}">
        <div class="flex items-center space-x-3">
            <i class="{{ $icon }} w-5 text-center"></i>
            <span>{{ $title }}</span>
        </div>
        <i class="fas fa-chevron-down text-xs transition-transform"></i>
    </button>
    <div id="{{ $id }}" class="dropdown-menu ml-8 mt-1 space-y-1 {{ $active ? 'active' : '' }}">
        @foreach ($items as $item)
            <x-sidebar-item :url="$item['url']" :label="$item['label']" />
        @endforeach
    </div>
</div>
