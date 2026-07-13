<x-mail::message>
# 💼 Invoice Notification

Hello **{{ $invoice->client->first_name }}**,

Your invoice **#{{ $invoice->invoice_number }}** has been generated.

---

## 📦 Package Information
**Name:** {{ $invoice->package_name }}
**Description:** {{ $invoice->package_description }}

---

## 💰 Payment Details
**Amount:** {{ $currency  }} {{ number_format($invoice->invoice_amount, 2) }}
**Status:**
@if($invoice->status == \App\Enums\InvoiceStatus::PAID)
✅ Paid
@elseif($invoice->status == \App\Enums\InvoiceStatus::UNPAID)
⏳ Unpaid
@elseif($invoice->status == \App\Enums\InvoiceStatus::CANCELLED)
❌ Cancelled
@elseif($invoice->status == \App\Enums\InvoiceStatus::REFUND_REQUESTED)
↩️ Refund Requested
@elseif($invoice->status == \App\Enums\InvoiceStatus::REFUNDED)
🔄 Refunded
@endif

**Issued Date:** {{ $invoice->created_at->format('M d, Y') }}
**Due Date:** {{ $invoice->due_date?->format('M d, Y') ?? 'Not specified' }}

---

<x-mail::button :url="route('client.invoices.show', $invoice->id)" color="success">
🔗 View Invoice
</x-mail::button>

{{-- @if($invoice->notes)
---
## 📝 Notes
{{ $invoice->notes }}
@endif --}}

Thanks for your business,
**{{ $siteName }}**
</x-mail::message>
