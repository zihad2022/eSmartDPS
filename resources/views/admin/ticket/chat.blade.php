<x-admin.layout.app>
    <div class="max-w-3xl mx-auto bg-white shadow rounded-xl h-[80vh] flex flex-col">
        <!-- Header -->
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h2 class="text-lg font-bold text-primary-900">
                Ticket #{{ $ticket->ticket_number }} - {{ $ticket->subject }}
            </h2>
        </div>

        <!-- Chat Messages -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
            @foreach ($ticket->replies as $message)
                <div class="flex {{ $message->admin_id ? 'justify-end' : 'justify-start' }}">
                    <div
                        class="max-w-[75%] px-4 py-3 rounded-xl shadow text-sm
                        {{ $message->admin_id ? 'bg-accent-500 text-white' : 'bg-white border' }}">
                        @if ($message->message)
                            <p>{{ $message->message }}</p>
                        @endif

                        @if ($message->attachment)
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $message->attachment) }}" target="_blank"
                                    class="underline text-xs">
                                    📎 {{ basename($message->attachment) }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Input Field -->
        <form action="{{ route('admin.tickets.message.store', $ticket->id) }}" method="POST"
            enctype="multipart/form-data" class="border-t p-4 flex items-center gap-3">
            @csrf

            <!-- Message Input -->
            <input type="text" name="message" placeholder="Type your message"
                class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-accent-500 focus:outline-none" />

            <!-- File Attachment -->
            <input type="file" name="attachment" class="hidden" id="attachmentInput">
            <label for="attachmentInput" class="cursor-pointer text-accent-600 text-lg" title="Attach file">
                <i class="fas fa-paperclip"></i>
            </label>

            <!-- Send Button -->
            <button type="submit"
                class="bg-accent-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-accent-600 transition">
                <i class="fas fa-paper-plane mr-1"></i>Send
            </button>
        </form>

    </div>
</x-admin.layout.app>
