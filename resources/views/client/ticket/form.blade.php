<x-client.layout.app title="{{ isset($ticket) ? 'Edit Ticket' : 'New Ticket' }}">@php($editing=isset($ticket))
<section class="page-head"><div><h1>{{ $editing?'Edit Ticket':'New Ticket' }}</h1></div><a class="secondary-btn" href="{{ route('client.tickets.index') }}">Cancel</a></section>
<form class="form-card" method="POST" action="{{ $editing ? route('client.tickets.update',$ticket) : route('client.tickets.store') }}">@csrf @if($editing)@method('PUT')@endif
<div class="form-grid">
<div class="field"><label>Ticket Number *</label><input value="{{ old('ticket_number',$ticket->ticket_number ?? $ticket_number) }}" disabled><input type="hidden" name="ticket_number" value="{{ old('ticket_number',$ticket->ticket_number ?? $ticket_number) }}"></div>
<div class="field"><label>Priority *</label><select name="priority" required>@foreach(\App\Enums\TicketPriority::cases() as $p)<option value="{{ $p->value }}" {{ old('priority',$ticket->priority?->value ?? \App\Enums\TicketPriority::MEDIUM->value)===$p->value?'selected':'' }}>{{ $p->label() }}</option>@endforeach</select></div>
<div class="field full"><label>Subject *</label><input name="subject" value="{{ old('subject',$ticket->subject ?? '') }}" required></div>
<div class="field full"><label>Message *</label><textarea name="message" required>{{ old('message',$ticket->message ?? '') }}</textarea></div>
@if($editing && !empty($ticket->admin_notes))<div class="field full"><label>Support note</label><textarea disabled>{{ $ticket->admin_notes }}</textarea></div>@endif
</div>@foreach($errors->all() as $error)<div class="field-error">{{ $error }}</div>@endforeach<div class="form-actions"><button class="primary-btn" type="submit">{{ $editing?'Update Ticket':'Create Ticket' }}</button></div></form>
</x-client.layout.app>