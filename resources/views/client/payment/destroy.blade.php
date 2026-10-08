<form method="POST" action="{{ route('client.payments.destroy', $payment->id) }}" class="delete-form">
    @csrf
    @method('DELETE')
    <button type="button" class="text-red-600 hover:text-red-900 delete-btn" title="Delete" data-confirm-title="Delete Payment" data-confirm-message="Are you sure you want to delete this payment?">
        <i class="fas fa-trash"></i>
    </button>
</form>
