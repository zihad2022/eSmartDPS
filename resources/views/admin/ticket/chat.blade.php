<x-admin.layout.app>
    <x-slot:title>Ticket Chat</x-slot:title>
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Tickets', 'url' => route('admin.tickets.index')],
        ['label' => '#'.$ticket->ticket_number.' Chat'],
    ]" />
    <livewire:admin.ticket.chat :ticket="$ticket" />
</x-admin.layout.app>
