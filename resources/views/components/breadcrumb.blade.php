@props(['items'])

<nav class="text-sm text-primary-500 mb-4" aria-label="Breadcrumb">
    <ol class="list-reset flex items-center space-x-2">
        @foreach ($items as $index => $item)
            @php
                $isLast = $loop->last;
            @endphp

            @if (!$isLast && !empty($item['url']))
                <li>
                    <a href="{{ $item['url'] }}" class="hover:text-accent-500 font-medium">
                        @if ($index === 0)
                            <i class="fas fa-home mr-1"></i>
                        @endif
                        {{ $item['label'] }}
                    </a>
                </li>
            @else
                <li class="text-primary-700 font-semibold">
                    @if ($index === 0)
                        <i class="fas fa-home mr-1"></i>
                    @endif
                    {{ $item['label'] }}
                </li>
            @endif

            @if (!$loop->last)
                <li><span class="mx-1 text-primary-400">/</span></li>
            @endif
        @endforeach
    </ol>
</nav>
