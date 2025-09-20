<x-client.layout.app>
    <x-slot:title>{{ $editing = isset($ticket) ? 'Edit Ticket' : 'Add New Ticket' }}</x-slot:title>
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('client.dashboard')],
        ['label' => 'All Tickets', 'url' => route('client.tickets.index')],
        ['label' => $editing ? 'Edit Ticket' : 'Add New Ticket'],
    ]" />
    <div>
        <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
            <h2 class="text-xl font-semibold text-primary-900 mb-6">
                {{ $editing ? 'Edit Ticket' : 'Add New Ticket' }}
            </h2>

            @php $editing = isset($ticket); @endphp

            <form method="POST"
                action="{{ $editing ? route('client.tickets.update', $ticket->id) : route('client.tickets.store') }}"
                class="space-y-8">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif

                {{-- Ticket Number --}}
                <x-form.input name="ticket_number" label="Ticket Number" 
                    :value="old('ticket_number', $ticket->ticket_number ?? $ticket_number)" 
                    required disabled />
                    <input type="hidden" name="ticket_number"
                    value="{{ old('ticket_number', $ticket->ticket_number ?? $ticket_number) }}">

                {{-- Subject --}}
                <x-form.input name="subject" label="Subject" 
                    :value="old('subject', $ticket->subject ?? '')" 
                    required placeholder="Enter ticket subject" />

                {{-- Message --}}
                <div>
                    <label for="message" class="block text-sm font-medium text-primary-700 mb-2">Message</label>
                    <textarea name="message" id="message" rows="5"
                        class="w-full border border-gray-300 rounded-lg text-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-accent-500"
                        placeholder="Describe the issue..." required>{{ old('message', $ticket->message ?? '') }}</textarea>
                    @error('message')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Priority --}}
                <x-form.select name="priority" label="Priority"
                    :options="collect(\App\Enums\TicketPriority::cases())
                        ->mapWithKeys(fn($type) => [$type->value => $type->label()])
                        ->toArray()" 
                    :selected="old('priority', $ticket->priority?->value ?? \App\Enums\TicketPriority::MEDIUM->value)" />

                    {{-- Admin Notes --}}
                    <div>
                        <label for="admin_notes" class="block text-sm font-medium text-primary-700 mb-2">
                            Admin Notes
                        </label>
                        <textarea id="admin_notes" rows="3"
                            class="w-full border border-gray-300 rounded-lg text-sm py-2 px-3 bg-gray-50 text-primary-900 focus:outline-none"
                            placeholder="Any notes or instructions from the admin will appear here..." readonly>{{ $ticket->admin_notes ?? '' }}</textarea>
                    </div>
                    
                {{-- Submit --}}
                <div class="flex justify-end space-x-4 pt-4">
                    <a href="{{ route('client.tickets.index') }}"
                        class="px-4 py-2 border border-gray-300 text-primary-700 rounded-lg hover:bg-gray-50 text-sm transition duration-300">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-accent-500 text-white text-sm rounded-lg hover:bg-accent-600 transition duration-300">
                        {{ $editing ? 'Update Ticket' : 'Create Ticket' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-client.layout.app>
