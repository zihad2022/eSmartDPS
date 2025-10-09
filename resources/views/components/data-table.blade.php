@props([
    'pageTitle' => 'Table', // Table title
    'rows' => null, // Collection or paginator
    'headers' => [], // Column headers
    'actions' => null, // Optional actions in header (Add, Export etc.)
    'sl' => null, // Optional starting serial
    'rowView' => null, // Optional partial view to render each row
])

@php
    $rows = $rows ?? collect();
    $sl = $sl ?? ($rows instanceof \Illuminate\Pagination\AbstractPaginator ? $rows->firstItem() : 1);
    $colspan = count($headers);
@endphp

<div class="bg-white rounded-xl shadow-sm">

    {{-- Table Header with title and optional actions --}}
    <div class="p-6 border-b border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">
                {{ $pageTitle }}
            </h3>

            @if ($actions)
                <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                    {{ $actions }}
                </div>
            @endif
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    @foreach ($headers as $header)
                        <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($rows as $row)
                    <tr class="hover:bg-gray-50">
                        {{-- Serial --}}
                        <td class="px-6 py-4 text-sm text-primary-900 font-mono">#{{ $sl++ }}</td>

                        {{-- Row content from provided partial or slot --}}
                        @if ($rowView)
                            @include($rowView, ['row' => $row])
                        @else
                            {{ $slot }}
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $colspan }}" class="text-center py-4 text-sm text-gray-500">
                            No data found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($rows instanceof \Illuminate\Pagination\AbstractPaginator)
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-primary-600">
                @if ($rows->total() > 0)
                    Showing {{ $rows->firstItem() }} to {{ $rows->lastItem() }} of {{ $rows->total() }} results
                @else
                    No results found.
                @endif
            </div>
            <div class="flex space-x-2">
                <x-pagination :paginator="$rows" />
            </div>
        </div>
    @endif

</div>
