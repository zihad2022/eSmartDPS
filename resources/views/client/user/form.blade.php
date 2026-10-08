<x-client.layout.app title="{{ isset($user) ? 'Edit User' : 'Add User' }}">@php($editing=isset($user))
<section class="page-head"><div><h1>{{ $editing ? 'Edit User' : 'Add New User' }}</h1></div><a class="secondary-btn" href="{{ route('client.users.index') }}">Cancel</a></section>
<form class="form-card" method="POST" action="{{ $editing ? route('client.users.update',$user) : route('client.users.store') }}" enctype="multipart/form-data">@csrf @if($editing)@method('PUT')@endif
<div class="form-section"><h3>Account details</h3><div class="form-grid">
<div class="field"><label>First name *</label><input name="first_name" value="{{ old('first_name',$user->first_name ?? '') }}" required></div>
<div class="field"><label>Last name *</label><input name="last_name" value="{{ old('last_name',$user->last_name ?? '') }}" required></div>
<div class="field"><label>Email *</label><input type="email" name="email" value="{{ old('email',$user->email ?? '') }}" {{ $editing?'readonly':'' }} required></div>
<div class="field"><label>Mobile *</label><input name="phone" value="{{ old('phone',$user->phone ?? '') }}" {{ $editing?'readonly':'' }} required></div>
<div class="field"><label>Password {{ $editing?'':'*' }}</label><input type="password" name="password" placeholder="{{ $editing?'Leave blank to keep current password':'Enter password' }}" {{ $editing?'':'required' }}></div>
<div class="field"><label>Role *</label><select name="role" required>@foreach(['admin'=>'Administrator','manager'=>'Manager','editor'=>'Editor'] as $value=>$label)<option value="{{ $value }}" {{ old('role',$user->role ?? 'manager')===$value?'selected':'' }}>{{ $label }}</option>@endforeach</select></div>
<div class="field"><label>Status *</label><select name="status" required><option value="1" {{ old('status',$user->status ?? 1)==1?'selected':'' }}>Active</option><option value="0" {{ old('status',$user->status ?? 1)==0?'selected':'' }}>Inactive</option></select></div>
<div class="field"><label>Profile photo</label><input type="file" name="profile_photo" accept="image/*"></div>
</div></div>
<div class="form-section"><h3>Optional information</h3><div class="form-grid">
<div class="field"><label>NID number</label><input name="nid_number" value="{{ old('nid_number',$user->nid_number ?? '') }}"></div>
<div class="field"><label>Division</label><input name="division" value="{{ old('division',$user->division ?? '') }}"></div>
<div class="field"><label>District</label><input name="district" value="{{ old('district',$user->district ?? '') }}"></div>
<div class="field"><label>Postal code</label><input name="postal_code" value="{{ old('postal_code',$user->postal_code ?? '') }}"></div>
<div class="field full"><label>Address</label><textarea name="address">{{ old('address',$user->address ?? '') }}</textarea></div>
<div class="field"><label>NID front</label><input type="file" name="nid_card_front" accept="image/*"></div>
<div class="field"><label>NID back</label><input type="file" name="nid_card_back" accept="image/*"></div>
</div></div>
@foreach($errors->all() as $error)<div class="field-error">{{ $error }}</div>@endforeach
<div class="form-actions"><button class="primary-btn" type="submit">{{ $editing?'Update User':'Create User' }}</button></div></form>
</x-client.layout.app>