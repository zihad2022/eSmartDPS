{{-- resources/views/admin/package/partials/row.blade.php --}}
@php($package = $row)
@php($currency = isset($settings) && ($settings->currency ?? null) ? $settings->currency : '$')

<td class="px-6 py-4 text-sm text-primary-900 font-semibold">{{ $package->name }}</td>
<td class="px-6 py-4 text-sm text-primary-900 font-mono">
    {{ $currency }} {{ number_format($package->price) }}
</td>
<td class="px-6 py-4 text-sm text-primary-900 font-mono">
    @if ($package->discount_value > 0)
        @if ($package->discount_type === \App\Enums\Package\DiscountType::FIXED)
            {{ $currency }} {{ number_format($package->discount_value) }}
        @else
            {{ $package->discount_value }}%
        @endif
    @else
        <span class="text-gray-500">N/A</span>
    @endif
</td>
<td class="px-6 py-4 text-sm text-primary-900 font-semibold">
    {{ $package->billing_cycle ? $package->billing_cycle->label() : '—' }}
</td>
<td class="px-6 py-4 text-sm text-primary-900 font-mono">{{ $package->member_limit }}</td>
<td class="px-6 py-4 text-sm text-primary-900 font-mono">{{ $package->user_limit }}</td>
<td class="px-6 py-4 text-sm text-primary-900 font-mono">{{ $package->project_limit }}</td>
<td class="px-6 py-4">
    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $package->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
        {{ $package->is_active ? 'Active' : 'Inactive' }}
    </span>
</td>
<td class="px-6 py-4 text-sm text-primary-900 font-semibold">
    @if ($package->has_trial)
        {{ $package->trial_days }} days
    @else
        <span class="text-gray-500">N/A</span>
    @endif
</td>
<td class="px-6 py-4 text-sm text-primary-600">{{ $package->created_at->format('M d, Y') }}</td>
<td class="px-6 py-4 text-sm font-medium">
    <div class="flex space-x-2">
        <a href="{{ route('admin.packages.edit', $package->id) }}" class="text-secondary-600 hover:text-secondary-900" title="Edit">
            <i class="fas fa-edit"></i>
        </a>
        <form method="POST" action="{{ route('admin.packages.destroy', $package->id) }}" class="delete-form">
            @csrf
            @method('DELETE')
            <button type="button" class="text-red-600 hover:text-red-900 delete-btn" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </div>
    <x-confirm-modal />
</td>
