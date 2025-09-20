<form method="POST" action="{{ route('client.tickets.destroy', $ticket->id) }}" class="delete-form">
    @csrf
    @method('DELETE')
    <button type="button" class="text-red-600 hover:text-red-900 delete-btn" title="Delete">
        <i class="fas fa-trash"></i>
    </button>
</form>
