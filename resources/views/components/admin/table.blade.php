<div class="bg-white rounded-xl shadow-sm">

    {{-- HEADER AREA --}}
    <div class="p-6 border-b border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <h3 class="text-lg font-semibold text-primary-900 mb-4 md:mb-0">
                {{ $pageTitle ?? '' }}
            </h3>

            @if (isset($header))
                <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                    {{ $header }}
                </div>
            @endif
        </div>
    </div>

    {{-- TABLE --}}
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    @foreach ($columns as $column)
                        <th class="px-6 py-3 text-left text-xs font-medium text-primary-500 uppercase tracking-wider">
                            {{ $column }}
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    {{-- FOOTER / PAGINATION --}}
    @if (isset($footer))
        <div class="border-t border-gray-200 p-6">
            {{ $footer }}
        </div>
    @endif

</div>
