<x-client.layout.app title="Ticket Chat">
<div class="smart-page-head"><h3 class="section-title"><i class="fas fa-comments"></i> #{{ $ticket->ticket_number }}</h3><div class="smart-page-actions"><a class="btn btn-outline" href="{{ route('client.tickets.index') }}"><i class="fas fa-arrow-left"></i></a></div></div>
<div class="smart-chat-card">
    <div class="smart-chat-head"><div><strong>{{ $ticket->subject }}</strong><div class="text-xs-bold-muted mt-5">SMART DPS Support</div></div><span class="custom-badge badge-pending">{{ method_exists($ticket->status,'label')?$ticket->status->label():ucfirst(str_replace('_',' ',$ticket->status->value ?? $ticket->status)) }}</span></div>
    <div class="smart-chat-body" id="chatMessages">
        @if(!empty($ticket->message))<div class="chat-row mine"><div class="chat-bubble">{{ $ticket->message }}<small>{{ $ticket->created_at?->format('h:i A') }}</small></div></div>@endif
        @foreach(($ticket->messages ?? collect()) as $message)<div class="chat-row {{ ($message->sender_type ?? '')==='client'?'mine':'' }}"><div class="chat-bubble">{{ $message->message ?? $message->body ?? '' }}<small>{{ $message->created_at?->format('h:i A') }}</small></div></div>@endforeach
    </div>
    <form class="smart-chat-compose" method="POST" action="{{ route('client.tickets.message.store',$ticket) }}">@csrf<input name="message" placeholder="Write a message..." required><button type="submit"><i class="fas fa-paper-plane"></i></button></form>
</div>
<div class="template-item"><div class="flex-between-mb10"><span class="text-xs-bold-muted">Priority</span><span class="text-xs-bold-800">{{ method_exists($ticket->priority,'label')?$ticket->priority->label():ucfirst($ticket->priority->value ?? $ticket->priority) }}</span></div><div class="flex-between-mb0"><span class="text-xs-bold-muted">Status</span><span class="text-xs-bold-800">{{ method_exists($ticket->status,'label')?$ticket->status->label():ucfirst($ticket->status->value ?? $ticket->status) }}</span></div></div>
@push('scripts')<script>const chat=document.getElementById('chatMessages');if(chat)chat.scrollTop=chat.scrollHeight;</script>@endpush
</x-client.layout.app>
