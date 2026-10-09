<x-client.layout.app title="Members">
<div class="smart-page-head"><h3 class="section-title"><i class="fas fa-users"></i> Members</h3><div class="smart-page-actions"><a class="btn btn-primary" href="{{ route('client.members.create') }}"><i class="fas fa-plus"></i> Add Member</a></div></div>
<div class="smart-filter"><a class="{{ !request('status')?'active':'' }}" href="{{ route('client.members.index') }}">All</a><a class="{{ request('status')==='active'?'active':'' }}" href="{{ route('client.members.index',['status'=>'active']) }}">Active</a><a class="{{ request('status')==='inactive'?'active':'' }}" href="{{ route('client.members.index',['status'=>'inactive']) }}">Inactive</a></div>
<form class="smart-search" method="GET">@if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif<i class="fas fa-search"></i><input name="search" value="{{ request('search') }}" placeholder="Search members..."></form>
<div class="template-list">
@forelse($members as $member)
<div class="template-item">
    <h4 class="template-name">{{ $member->name }} <span class="custom-badge {{ $member->status?'badge-approved':'badge-rejected' }}">{{ $member->status?'Active':'Inactive' }}</span></h4>
    <p class="template-excerpt">{{ $member->member_id }} · {{ $member->phone ?: ($member->email ?: 'No contact') }}</p>
    <div class="template-footer"><span>{{ number_format($member->share_quantity) }} shares · {{ $settings->currency ?? '৳' }} {{ number_format($member->total_balance) }}</span><div class="template-actions">
        <button class="view-btn" type="button" onclick="openModal('member-view-{{ $member->id }}')"><i class="fas fa-eye"></i></button>
        <a class="view-btn" href="{{ route('client.members.edit',$member) }}"><i class="fas fa-edit"></i></a>
        <form method="POST" action="{{ route('client.members.destroy',$member) }}">@csrf @method('DELETE')<button class="btn-contact-action btn-delete-mini delete-btn" type="button" data-confirm-title="Delete Member" data-confirm-message="Are you sure you want to delete this member?"><i class="fas fa-trash"></i></button></form>
    </div></div>
</div>
<x-client.record-modal
    :id="'member-view-'.$member->id"
    title="Member Details"
    :fields="[
        ['label'=>'Member ID','value'=>$member->member_id ?: '—'],
        ['label'=>'Status','value'=>$member->status ? 'Active' : 'Inactive'],
        ['label'=>'Full Name','value'=>$member->name ?: '—','full'=>true],
        ['label'=>'Email','value'=>$member->email ?: '—'],
        ['label'=>'Phone','value'=>$member->phone ?: '—'],
        ['label'=>'Share Quantity','value'=>number_format($member->share_quantity ?? 0)],
        ['label'=>'Total Balance','value'=>($settings->currency ?? '৳').' '.number_format($member->total_balance ?? 0)],
    ]"
    :actions="[['label'=>'Edit Member','url'=>route('client.members.edit',$member)]]"
/>
@empty<div class="smart-empty">No members found.</div>@endforelse
</div>
<div class="mt-20"><x-pagination :paginator="$members" /></div>
</x-client.layout.app>
