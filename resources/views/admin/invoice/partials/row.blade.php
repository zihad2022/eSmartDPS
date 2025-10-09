{{-- resources/views/admin/invoice/partials/row.blade.php --}}
@php($invoice = $row)
@php($currency = isset($settings) && ($settings->currency ?? null) ? $settings->currency : '$')

<td class="px-6 py-4 text-sm text-primary-900 font-mono">{{ $invoice->invoice_number }}</td>
<td class="px-6 py-4 text-sm text-primary-700">{{ $invoice->client->first_name }} {{ $invoice->client->last_name }}</td>
<td class="px-6 py-4 text-sm text-primary-900 font-semibold">{{ $invoice->package_name }}</td>
<td class="px-6 py-4 text-sm text-primary-900 font-mono">{{ $currency }} {{ number_format($invoice->invoice_amount) }}</td>
<td class="px-6 py-4 text-sm text-primary-700">{{ $invoice->payment_method?->label() ?? 'N/A' }}</td>
<td class="px-6 py-4">
    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $invoice->status->bgColor() }} {{ $invoice->status->color() }}">
        {{ $invoice->status->label() }}
    </span>
</td>
<td class="px-6 py-4 text-sm text-primary-600">
    @php($isPastDue = $invoice->due_date && $invoice->due_date->isPast() && !$invoice->paid_at)
    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $isPastDue ? 'text-red-600 bg-red-100' : 'text-green-600 bg-green-100' }}">
        {{ $invoice->due_date?->format('M d, Y') ?? '-' }}
    </span>
</td>
<td class="px-6 py-4 text-sm text-primary-600">
    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $invoice->paid_at ? 'text-green-600 bg-green-100' : 'text-gray-600 bg-gray-100' }}">
        {{ $invoice->paid_at?->format('M d, Y') ?? '-' }}
    </span>
</td>
<td class="px-6 py-4 text-sm text-primary-600">{{ $invoice->created_at->format('M d, Y') }}</td>
<td class="px-6 py-4 text-sm font-medium">
    <div class="flex space-x-2">
        <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="text-accent-600 hover:text-accent-900" title="View">
            <i class="fas fa-eye"></i>
        </a>
        <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="text-secondary-600 hover:text-secondary-900" title="Edit">
            <i class="fas fa-edit"></i>
        </a>
        <form method="POST" action="{{ route('admin.invoices.destroy', $invoice->id) }}" class="delete-form">
            @csrf
            @method('DELETE')
            <button type="button" class="text-red-600 hover:text-red-900 delete-btn" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </div>
    <x-confirm-modal />
</td>
