@switch($status)
    @case(App\Enums\TicketStatus::OPEN)
        <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Open</span>
    @break

    @case(App\Enums\TicketStatus::IN_PROGRESS)
        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">In Progress</span>
    @break

    @case(App\Enums\TicketStatus::RESOLVED)
        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Resolved</span>
    @break

    @case(App\Enums\TicketStatus::CLOSED)
        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Closed</span>
    @break

    @default
        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Unknown</span>
@endswitch
