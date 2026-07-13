<?php

namespace App\Actions\Admin\Tickets;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;

class GetTicketsAction
{
    public function execute(?string $search, ?string $status, ?string $priority, int $perPage = 10): array
    {
        $statusMap = [
            'open' => TicketStatus::OPEN,
            'in_progress' => TicketStatus::IN_PROGRESS,
            'resolved' => TicketStatus::RESOLVED,
            'closed' => TicketStatus::CLOSED,
        ];

        $priorityMap = [
            'low' => TicketPriority::LOW,
            'medium' => TicketPriority::MEDIUM,
            'high' => TicketPriority::HIGH,
        ];

        $tickets = Ticket::query()
            ->with('client:id,first_name,last_name,user_id,email,phone')
            ->when(filled($search), function ($query) use ($search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested->where('ticket_number', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%")
                        ->orWhereHas('client', function ($clientQuery) use ($search): void {
                            $clientQuery->where(function ($client) use ($search): void {
                                $client->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('user_id', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%")
                                    ->orWhere('phone', 'like', "%{$search}%");
                            });
                        });
                });
            })
            ->when(isset($statusMap[$status]), fn ($query) => $query->where('status', $statusMap[$status]))
            ->when(isset($priorityMap[$priority]), fn ($query) => $query->where('priority', $priorityMap[$priority]))
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        $statusCounts = Ticket::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->map(fn ($count): int => (int) $count)
            ->all();

        $priorityCounts = Ticket::query()
            ->selectRaw('priority, COUNT(*) as aggregate')
            ->groupBy('priority')
            ->pluck('aggregate', 'priority')
            ->map(fn ($count): int => (int) $count)
            ->all();

        return [
            'tickets' => $tickets,
            'totalTickets' => array_sum($statusCounts),
            'statusCounts' => $statusCounts,
            'priorityCounts' => $priorityCounts,
            'search' => $search,
        ];
    }
}
