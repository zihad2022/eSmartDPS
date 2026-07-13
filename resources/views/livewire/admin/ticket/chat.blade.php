<div class="max-w-4xl mx-auto bg-white shadow rounded-xl h-[78vh] flex flex-col">
    <div class="px-6 py-4 border-b flex flex-col gap-2 sm:flex-row sm:justify-between sm:items-center">
        <div>
            <h2 class="text-lg font-bold text-primary-900">Ticket #{{ $ticket->ticket_number }} — {{ $ticket->subject }}</h2>
            <p class="text-xs text-primary-500">{{ $ticket->client?->full_name ?? 'Unknown client' }}</p>
        </div>
        <a href="{{ route('admin.tickets.show', $ticket) }}" class="text-sm text-accent-600 hover:text-accent-800">
            <i class="fas fa-eye mr-1"></i>Ticket details
        </a>
    </div>

    <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50" wire:poll.15s>
        @forelse ($ticket->replies as $reply)
            <div class="flex {{ $reply->admin_id ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[80%] px-4 py-3 rounded-xl shadow-sm text-sm {{ $reply->admin_id ? 'bg-accent-500 text-white' : 'bg-white border border-gray-200 text-primary-800' }}">
                    <p class="mb-1 text-[11px] font-medium {{ $reply->admin_id ? 'text-white/80' : 'text-primary-500' }}">
                        {{ $reply->admin?->name ?? $reply->client?->full_name ?? ($reply->admin_id ? 'Admin' : 'Client') }}
                    </p>

                    @if ($reply->message)
                        <p class="whitespace-pre-line">{{ $reply->message }}</p>
                    @endif

                    @if ($reply->attachment)
                        <div class="mt-2">
                            <a href="{{ asset('storage/'.$reply->attachment) }}" target="_blank" rel="noopener"
                                class="underline text-xs {{ $reply->admin_id ? 'text-white' : 'text-accent-600' }}">
                                <i class="fas fa-paperclip mr-1"></i>{{ basename($reply->attachment) }}
                            </a>
                        </div>
                    @endif

                    <p class="mt-2 text-[10px] {{ $reply->admin_id ? 'text-white/70' : 'text-primary-400' }}">
                        {{ $reply->created_at->format('M d, Y h:i A') }}
                    </p>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 text-sm py-8">No messages yet.</p>
        @endforelse
    </div>

    @if (auth('admin')->user()->can('send ticket messages'))
    <div class="border-t p-4">
        @error('message')
            <p class="mb-2 text-xs text-red-600">{{ $message }}</p>
        @enderror
        @error('attachment')
            <p class="mb-2 text-xs text-red-600">{{ $message }}</p>
        @enderror

        @if ($attachment)
            <div class="mb-3 flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2 text-xs text-primary-600">
                <span><i class="fas fa-paperclip mr-1"></i>{{ $attachment->getClientOriginalName() }}</span>
                <button type="button" wire:click="$set('attachment', null)" class="text-red-600 hover:text-red-800" aria-label="Remove attachment">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <form wire:submit="sendMessage" class="flex items-center gap-3">
            <input type="text" wire:model="message" wire:key="message-input-{{ $messageKey }}"
                placeholder="Type your message..."
                class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-accent-500 focus:outline-none" />

            <input type="file" id="chatAttachment" wire:model="attachment" class="hidden"
                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip" />

            <label for="chatAttachment" class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 cursor-pointer border border-gray-300" title="Attach file">
                <i class="fas fa-paperclip text-accent-600"></i>
            </label>

            <button type="submit" wire:loading.attr="disabled" wire:target="sendMessage,attachment"
                class="px-4 py-2 rounded-lg text-sm text-white transition disabled:opacity-60 {{ filled($message) || $attachment ? 'bg-accent-500 hover:bg-accent-600' : 'bg-gray-300 cursor-not-allowed' }}"
                @disabled(blank($message) && ! $attachment)>
                <span wire:loading.remove wire:target="sendMessage"><i class="fas fa-paper-plane mr-1"></i>Send</span>
                <span wire:loading wire:target="sendMessage"><i class="fas fa-spinner fa-spin mr-1"></i>Sending</span>
            </button>
        </form>
    </div>
    @else
        <div class="border-t p-4 text-center text-sm text-primary-500">You can view this conversation, but you do not have permission to send messages.</div>
    @endif
</div>
