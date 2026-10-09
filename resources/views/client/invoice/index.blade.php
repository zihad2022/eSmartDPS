<x-client.layout.app title="Invoices">@php($currency=$settings->currency ?? '৳')
<h3 class="section-title"><i class="fas fa-file-invoice"></i> Invoices</h3>
<div class="smart-filter"><a class="{{ !request('status')?'active':'' }}" href="{{ route('client.invoices.index') }}">All</a><a class="{{ request('status')==='unpaid'?'active':'' }}" href="{{ route('client.invoices.index',['status'=>'unpaid']) }}">Unpaid</a><a class="{{ request('status')==='paid'?'active':'' }}" href="{{ route('client.invoices.index',['status'=>'paid']) }}">Paid</a></div>
<div class="template-list">
@forelse($invoices as $invoice)
@php($status=$invoice->status->value ?? strtolower($invoice->status->label()))
<div class="template-item">
    <h4 class="template-name">{{ $invoice->package_name }} <span class="custom-badge {{ $status==='paid'?'badge-approved':'badge-pending' }}">{{ $invoice->status->label() }}</span></h4>
    <p class="template-excerpt">{{ $invoice->invoice_number }} · {{ $currency }} {{ number_format($invoice->invoice_amount) }}</p>
    <div class="template-footer"><span>{{ $invoice->created_at?->format('d M Y') }}</span><div class="template-actions">
        <button class="view-btn" type="button" onclick="openModal('invoice-view-{{ $invoice->id }}')"><i class="fas fa-eye"></i></button>
        @if($status==='unpaid')<a class="btn-contact-action btn-contact-send" href="{{ route('client.checkout.create',$invoice) }}"><i class="fas fa-credit-card"></i></a>@endif
    </div></div>
</div>
<x-client.record-modal
    :id="'invoice-view-'.$invoice->id"
    title="Invoice Details"
    :fields="[
        ['label'=>'Invoice','value'=>$invoice->invoice_number ?: '—'],
        ['label'=>'Status','value'=>$invoice->status->label()],
        ['label'=>'Package','value'=>$invoice->package_name ?: '—','full'=>true],
        ['label'=>'Amount','value'=>$currency.' '.number_format($invoice->invoice_amount ?? 0)],
        ['label'=>'Payment Method','value'=>$invoice->payment_method?->label() ?? '—'],
        ['label'=>'Created','value'=>$invoice->created_at?->format('d M Y') ?? '—'],
        ['label'=>'Paid','value'=>$invoice->paid_at?->format('d M Y') ?? '—'],
    ]"
    :actions="$status==='unpaid' ? [['label'=>'Pay Invoice','url'=>route('client.checkout.create',$invoice),'class'=>'record-view-btn-success']] : []"
/>
@empty<div class="smart-empty">No invoices found.</div>@endforelse
</div><div class="mt-20"><x-pagination :paginator="$invoices" /></div>
</x-client.layout.app>
