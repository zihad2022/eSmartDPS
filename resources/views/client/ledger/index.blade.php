<x-client.layout.app title="Ledgers">@php($currency=$settings->currency ?? '৳')
<div class="smart-page-head"><h3 class="section-title"><i class="fas fa-book"></i> Ledgers</h3><div class="smart-page-actions"><a class="btn btn-primary" href="{{ route('client.ledgers.create') }}"><i class="fas fa-plus"></i> Add Entry</a></div></div>
<div class="smart-filter"><a class="{{ !request('type')?'active':'' }}" href="{{ route('client.ledgers.index') }}">All</a><a class="{{ request('type')==='income'?'active':'' }}" href="{{ route('client.ledgers.index',['type'=>'income']) }}">Income</a><a class="{{ request('type')==='expense'?'active':'' }}" href="{{ route('client.ledgers.index',['type'=>'expense']) }}">Expense</a><a href="{{ route('client.ledgers.report') }}">Report</a></div>
<div class="template-list">
@forelse($ledgers as $ledger)
@php($isIncome=$ledger->type == \App\Enums\Ledger\LedgerType::INCOME)
@php($typeLabel=ucfirst($ledger->type->value ?? $ledger->type))
<div class="template-item">
    <h4 class="template-name">{{ $ledger->description }} <span class="custom-badge {{ $isIncome?'badge-approved':'badge-rejected' }}">{{ $isIncome?'Income':'Expense' }}</span></h4>
    <p class="template-excerpt">{{ $ledger->ledgerCategory->name ?? 'Uncategorized' }} · {{ $currency }} {{ number_format($ledger->amount) }}</p>
    <div class="template-footer"><span>{{ \Carbon\Carbon::parse($ledger->entry_date)->format('d M Y') }}</span><div class="template-actions">
        <button class="view-btn" type="button" onclick="openModal('ledger-view-{{ $ledger->id }}')"><i class="fas fa-eye"></i></button>
        <a class="view-btn" href="{{ route('client.ledgers.edit',$ledger) }}"><i class="fas fa-edit"></i></a>
        <form method="POST" action="{{ route('client.ledgers.destroy',$ledger) }}">@csrf @method('DELETE')<button type="button" class="btn-contact-action btn-delete-mini delete-btn" data-confirm-title="Delete Ledger Entry" data-confirm-message="Are you sure you want to delete this ledger entry?"><i class="fas fa-trash"></i></button></form>
    </div></div>
</div>
<x-client.record-modal
    :id="'ledger-view-'.$ledger->id"
    title="Ledger Details"
    :fields="[
        ['label'=>'Type','value'=>$typeLabel],
        ['label'=>'Amount','value'=>$currency.' '.number_format($ledger->amount ?? 0)],
        ['label'=>'Category','value'=>$ledger->ledgerCategory->name ?? '—'],
        ['label'=>'Date','value'=>\Carbon\Carbon::parse($ledger->entry_date)->format('d M Y')],
        ['label'=>'Description','value'=>$ledger->description ?: '—','full'=>true],
        ['label'=>'Notes','value'=>$ledger->notes ?: '—','full'=>true],
    ]"
    :actions="[['label'=>'Edit Ledger','url'=>route('client.ledgers.edit',$ledger)]]"
/>
@empty<div class="smart-empty">No ledger entries found.</div>@endforelse
</div><div class="mt-20"><x-pagination :paginator="$ledgers" /></div>
</x-client.layout.app>
