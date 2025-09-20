<div class="max-w-3xl mx-auto bg-white shadow rounded-xl h-[80vh] flex flex-col">

    {{-- Header --}}
    <div class="px-6 py-4 border-b flex justify-between items-center">
        <h2 class="text-lg font-bold text-primary-900">
            Ticket #{{ $ticket->ticket_number }} - {{ $ticket->subject }}
        </h2>
    </div>

    {{-- Messages --}}
    <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
        @forelse ($ticket->replies as $reply)
            <div class="flex {{ $reply->client_id ? 'justify-end' : 'justify-start' }}">
                <div
                    class="max-w-[75%] px-4 py-3 rounded-xl shadow text-sm
                    {{ $reply->client_id ? 'bg-accent-500 text-white' : 'bg-white border' }}">

                    @if ($reply->message)
                        <p>{{ $reply->message }}</p>
                    @endif

                    @if ($reply->attachment)
                        <div class="mt-2">
                            <a href="{{ asset('storage/' . $reply->attachment) }}" target="_blank"
                                class="underline text-xs text-blue-600">
                                📎 {{ basename($reply->attachment) }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 text-sm">No messages yet.</p>
        @endforelse
    </div>

    {{-- Message Input --}}
    <form wire:submit.prevent="sendMessage" class="border-t p-4 flex items-center gap-3">

        {{-- Message Input --}}
        <input type="text" wire:model.live="message" wire:key="message-input-{{ $messageKey }}"
            placeholder="Type your message..."
            class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm 
                   focus:ring-2 focus:ring-accent-500 focus:outline-none" />

        {{-- File Attachment Input (Hidden) --}}
        <input type="file" id="chatAttachment" wire:model="attachment" class="hidden" />

        {{-- File Upload Button --}}
        <label for="chatAttachment"
            class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 
                   cursor-pointer border border-gray-300">
            <i class="fas fa-paperclip text-accent-600"></i>
        </label>

        {{-- Send Button --}}
        <button type="submit"
            class="px-4 py-2 rounded-lg text-sm text-white transition
            {{ $message || $attachment ? 'bg-accent-500 hover:bg-accent-600' : 'bg-gray-300 cursor-not-allowed' }}"
            @disabled(!($message || $attachment))>
            <i class="fas fa-paper-plane mr-1"></i> Send
        </button>
    </form>

</div>
