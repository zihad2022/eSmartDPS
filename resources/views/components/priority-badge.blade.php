@switch($priority)
    @case(App\Enums\TicketPriority::LOW)
        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Low</span>
    @break

    @case(App\Enums\TicketPriority::MEDIUM)
        <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Medium</span>
    @break

    @case(App\Enums\TicketPriority::HIGH)
        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">High</span>
    @break

    @default
        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Unknown</span>
@endswitch
