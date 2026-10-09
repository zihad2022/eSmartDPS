<x-member.layout.app title="Submit Payment">
@php $currency = $settings->currency ?? '৳'; @endphp

<div class="smart-page-head">
    <div>
        <h3 class="section-title"><i class="fas fa-credit-card"></i> Submit Payment</h3>
        <p>Send your payment information and receipt for verification.</p>
    </div>
</div>

<form action="{{ route('member.payment.store') }}" method="POST" enctype="multipart/form-data" class="smart-form-card">
    @csrf
    <div class="smart-form-grid">
        <div class="smart-field">
            <label for="payment_amount">Payment Amount *</label>
            <input id="payment_amount" type="number" name="payment_amount" step="0.01" min="0" value="{{ old('payment_amount') }}" placeholder="{{ $currency }} 0.00" required>
            @error('payment_amount')<div class="field-error">{{ $message }}</div>@enderror
        </div>

        <div class="smart-field">
            <label for="payment_date">Payment Date *</label>
            <input id="payment_date" type="date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
            @error('payment_date')<div class="field-error">{{ $message }}</div>@enderror
        </div>

        <div class="smart-field full">
            <label for="payment_method">Payment Method *</label>
            <select id="payment_method" name="payment_method" required>
                <option value="">Select payment method</option>
                @foreach ($methods as $method)
                    <option value="{{ $method->value }}" @selected((string) old('payment_method') === (string) $method->value)>{{ $method->label() }}</option>
                @endforeach
            </select>
            @error('payment_method')<div class="field-error">{{ $message }}</div>@enderror
        </div>

        <div class="smart-field full">
            <label for="reference_number">Reference Number</label>
            <input id="reference_number" type="text" name="reference_number" value="{{ old('reference_number') }}" placeholder="Transaction ID, check number, or reference">
            @error('reference_number')<div class="field-error">{{ $message }}</div>@enderror
        </div>

        <div class="smart-field full">
            <label for="payment_notes">Additional Notes</label>
            <textarea id="payment_notes" name="payment_notes" placeholder="Optional payment details...">{{ old('payment_notes') }}</textarea>
            @error('payment_notes')<div class="field-error">{{ $message }}</div>@enderror
        </div>

        <div class="smart-field full">
            <label for="receipt_file">Payment Receipt *</label>
            <input id="receipt_file" type="file" name="receipt_file" accept="image/*,.pdf" required>
            @error('receipt_file')<div class="field-error">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="member-info-note">
        <i class="fas fa-info-circle"></i>
        <div><strong>Receipt guidelines</strong><span>Upload a clear image or PDF showing the amount, payment date and transaction/reference information. Maximum file size follows your configured upload validation.</span></div>
    </div>

    <div class="smart-form-actions">
        <a href="{{ route('member.dashboard') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Submit Payment</button>
    </div>
</form>
</x-member.layout.app>
