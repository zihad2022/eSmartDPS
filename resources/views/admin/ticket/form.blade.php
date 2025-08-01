<x-admin.layout.app>
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
                    <div>
                        <label for="status" class="block text-sm font-medium text-primary-700 mb-2">Status</label>
                        <select name="status" id="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent-500"
                            required>
                            <option value="1" {{ old('status', $ticket->status ?? 1) == 1 ? 'selected' : '' }}>
                                Open</option>
                            <option value="2" {{ old('status', $ticket->status ?? 1) == 2 ? 'selected' : '' }}>
                                Resolved</option>
                            <option value="3" {{ old('status', $ticket->status ?? 1) == 3 ? 'selected' : '' }}>
                                Closed</option>
                        </select>
                        @error('status')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-primary-700 mb-2">Priority</label>
                        <select name="priority" id="priority"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent-500"
                            required>
                            <option value="1"
                                {{ old('priority', $ticket->priority ?? 2) == 1 ? 'selected' : '' }}>Low</option>
                            <option value="2"
                                {{ old('priority', $ticket->priority ?? 2) == 2 ? 'selected' : '' }}>Medium</option>
                            <option value="3"
                                {{ old('priority', $ticket->priority ?? 2) == 3 ? 'selected' : '' }}>High</option>
                        </select>
                        @error('priority')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- 🛠 Admin Notes --}}
                <div>
                    <label for="admin_notes" class="block text-sm font-medium text-primary-700 mb-2">Admin Notes</label>
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
