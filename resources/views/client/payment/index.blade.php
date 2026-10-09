<x-client.layout.app title="Payments">@php($currency=$settings->currency ?? '৳')
<h3 class="section-title"><i class="fas fa-credit-card"></i> Payments</h3>
<div class="smart-filter"><a class="{{ !request('status')?'active':'' }}" href="{{ route('client.payments.index') }}">All</a><a class="{{ request('status')==='pending'?'active':'' }}" href="{{ route('client.payments.index',['status'=>'pending']) }}">Pending</a><a class="{{ request('status')==='due'?'active':'' }}" href="{{ route('client.payments.index',['status'=>'due']) }}">Due</a><a class="{{ request('status')==='paid'?'active':'' }}" href="{{ route('client.payments.index',['status'=>'paid']) }}">Paid</a><a class="{{ request('status')==='cancelled'?'active':'' }}" href="{{ route('client.payments.index',['status'=>'cancelled']) }}">Cancelled</a></div>
<form class="smart-search" method="GET"><i class="fas fa-search"></i><input name="search" value="{{ request('search') }}" placeholder="Search payments..."></form>
<div class="template-list">
@forelse($payments as $payment)
@php($status=$payment->status->value ?? strtolower($payment->status->label()))
<div class="template-item">
    <h4 class="template-name">{{ $payment->member->name ?? $payment->payment_id }} <span class="custom-badge {{ $status==='paid'?'badge-approved':($status==='cancelled'?'badge-rejected':'badge-pending') }}">{{ $payment->status->label() }}</span></h4>
    <p class="template-excerpt">{{ $payment->payment_id }} · {{ $currency }} {{ number_format($payment->amount) }}</p>
    <div class="template-footer"><span>{{ $payment->created_at?->format('d M Y') }}</span><div class="template-actions">
        <button class="view-btn" type="button" onclick="openModal('payment-view-{{ $payment->id }}')"><i class="fas fa-eye"></i></button>
        <a class="view-btn" href="{{ route('client.payments.edit',$payment) }}"><i class="fas fa-edit"></i></a>
        <form method="POST" action="{{ route('client.payments.destroy',$payment) }}">@csrf @method('DELETE')<button type="button" class="btn-contact-action btn-delete-mini delete-btn" data-confirm-title="Delete Payment" data-confirm-message="Are you sure you want to delete this payment?"><i class="fas fa-trash"></i></button></form>
    </div></div>
</div>
<x-client.record-modal
    :id="'payment-view-'.$payment->id"
    title="Payment Details"
    :fields="[
        ['label'=>'Payment ID','value'=>$payment->payment_id ?: '—'],
        ['label'=>'Status','value'=>$payment->status->label()],
        ['label'=>'Member','value'=>$payment->member->name ?? '—','full'=>true],
        ['label'=>'Amount','value'=>($payment->currency ?? 'BDT').' '.number_format($payment->amount ?? 0)],
        ['label'=>'Method','value'=>$payment->payment_method?->label() ?? '—'],
        ['label'=>'Paid At','value'=>$payment->paid_at?->format('d M Y, h:i A') ?? '—'],
        ['label'=>'Due Date','value'=>$payment->due_date?->format('d M Y') ?? '—'],
        ['label'=>'Transaction ID','value'=>$payment->transaction_id ?: '—','full'=>true],
    ]"
    :actions="[['label'=>'Edit Payment','url'=>route('client.payments.edit',$payment)]]"
/>
@empty<div class="smart-empty">No payments found.</div>@endforelse
</div><div class="mt-20"><x-pagination :paginator="$payments" /></div>
</x-client.layout.app>
