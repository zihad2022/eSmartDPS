<x-admin.layout.app>
    <x-slot:title>{{ $editing = isset($ticket) ? 'Edit Ticket' : 'Add New Ticket' }}</x-slot:title>
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'All Tickets', 'url' => route('admin.tickets.index')],
        ['label' => $editing ? 'Edit Ticket' : 'Add New Ticket'],
    ]" />
    <div>
        <div class="bg-white rounded-2xl shadow-sm p-6 w-full mx-auto">
            <h2 class="text-xl font-semibold text-primary-900 mb-6">
                {{ $editing = isset($ticket) ? 'Edit Ticket' : 'Add New Ticket' }}
            </h2>

            @php $editing = isset($ticket); @endphp

            <form method="POST"
                action="{{ $editing ? route('admin.tickets.update', $ticket->id) : route('admin.tickets.store') }}"
                class="space-y-8">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif

                {{-- 🎫 Ticket Number --}}
                <x-form.input name="ticket_number" label="Ticket Number" :value="old('ticket_number', $ticket->ticket_number ?? $ticket_number)" required disabled />

                {{-- 👤 Client --}}
                <div>
                    <label for="client_id" class="block text-sm font-medium text-primary-700 mb-2">Select Client</label>
                    <select name="client_id" id="client_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent-500"
                        required>
                        <option value="">-- Select Client --</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}"
                                {{ old('client_id', $ticket->client_id ?? '') == $client->id ? 'selected' : '' }}>
                                {{ $client->first_name }} {{ $client->last_name }} ({{ $client->user_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('client_id')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 📝 Subject --}}
                <x-form.input name="subject" label="Subject" :value="old('subject', $ticket->subject ?? '')" required
                    placeholder="Enter ticket subject" />

                {{-- 💬 Message --}}
                <div>
                    <label for="message" class="block text-sm font-medium text-primary-700 mb-2">Message</label>
                    <textarea name="message" id="message" rows="5"
                        class="w-full border border-gray-300 rounded-lg text-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-accent-500"
                        placeholder="Describe the issue..." required>{{ old('message', $ticket->message ?? '') }}</textarea>
                    @error('message')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 📌 Status and Priority --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.select name="status" label="Status" :options="collect(\App\Enums\TicketStatus::cases())
                        ->mapWithKeys(fn($type) => [$type->value => $type->label()])
                        ->toArray()" :selected="old('status', $ticket->status?->value ?? '')" />

                    <x-form.select name="priority" label="Priority" :options="collect(\App\Enums\TicketPriority::cases())
                        ->mapWithKeys(fn($type) => [$type->value => $type->label()])
                        ->toArray()" :selected="old('priority', $ticket->priority?->value ?? '')" />
                </div>

                {{-- 🛠 Admin Notes --}}
                <div>
                    <label for="admin_notes" class="block text-sm font-medium text-primary-700 mb-2">Admin
                        Notes</label>
                    <textarea name="admin_notes" id="admin_notes" rows="3"
                        class="w-full border border-gray-300 rounded-lg text-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-accent-500"
                        placeholder="Optional notes from admin...">{{ old('admin_notes', $ticket->admin_notes ?? '') }}</textarea>
                    @error('admin_notes')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 🔘 Submit --}}
                <div class="flex justify-end space-x-4 pt-4">
                    <a href="{{ route('admin.tickets.index') }}"
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
</x-admin.layout.app>
