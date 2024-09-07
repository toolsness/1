<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold text-gray-800">Messages</h1>
        <button wire:click="toggleNewMessageForm"
            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
            New Message
        </button>
    </div>

    <div class="mb-4">
        <input wire:model.live="search" type="text" placeholder="Search messages..."
            class="w-full px-3 py-2 border rounded-md">
    </div>

    <div class="flex mb-4">
        <button wire:click="switchMessageType('inbox')"
            class="mr-4 {{ $messageType === 'inbox' ? 'font-bold rounded bg-slate-300 py-2 px-4' : 'rounded py-2 px-4' }}">Inbox</button>
        <button wire:click="switchMessageType('sent')"
            class="{{ $messageType === 'sent' ? 'font-bold rounded bg-slate-300 py-2 px-4' : 'rounded py-2 px-4' }}">Sent</button>
    </div>

    <div class="flex flex-col md:flex-row gap-6">
        <div class="w-full md:w-1/3 bg-white shadow-md rounded-lg p-4 border-2 border-black border-solid">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b">Message List</h2>
            @if ($messagePaginator->isNotEmpty())
                @foreach ($messagePaginator as $message)
                    <div wire:click="selectMessage({{ $message->id }})"
                        class="cursor-pointer border-b border-black py-2 {{ $message->read_at && $messageType === 'inbox' ? 'opacity-50' : '' }} {{ $selectedMessage && $selectedMessage->id == $message->id ? 'bg-blue-100' : '' }}">
                        <div class="flex justify-between items-center px-1">
                            <span class="font-semibold text-gray-700">{{ $message->title }}</span>
                            <span class="text-sm text-gray-500">{{ $message->sent_at->format('M d, Y') }}</span>
                        </div>
                        <p class="text-sm text-gray-600 truncate px-1">{{ $message->content }}</p>
                    </div>
                @endforeach
                {{ $messagePaginator->links() }}
            @else
                <p class="text-gray-600 text-center">No messages found.</p>
            @endif
        </div>

        <div class="w-full md:w-2/3 bg-white shadow-md rounded-lg p-4 border-2 border-black border-solid">
            @if ($selectedMessage)
                <h2 class="font-semibold mb-4 text-gray-800"><span class="text-xl underline">Subject:</span> <span
                        class="text-lg">{{ $selectedMessage->title }}</span></h2>
                <div class="mb-4">
                    <p class="text-sm text-gray-600">From: {{ $selectedMessage->sender->name }}</p>
                    <p class="text-sm text-gray-600">To: {{ $selectedMessage->receiver->name }}</p>
                    @if ($messageType === 'inbox' && $selectedMessage->sent_at)
                        <p class="text-sm text-gray-600">Received at:
                            {{ $selectedMessage->sent_at->format('M d, Y H:i') }}</p>
                    @else
                        <p class="text-sm text-gray-500">Sent at: {{ $selectedMessage->sent_at->format('M d, Y H:i') }}
                        </p>
                    @endif
                    <p class="text-sm text-gray-600">Inquiry Type: {{ ucfirst($selectedMessage->inquiry_type) }}</p>
                    @if ($messageType === 'sent' && $selectedMessage->read_at)
                        <p class="text-sm text-gray-500">Seen at: {{ $selectedMessage->read_at->format('M d, Y H:i') }}
                        </p>
                    @elseif ($messageType === 'sent' && $selectedMessage->sent_at)
                        <p class="text-sm text-gray-500">Read: Not seen yet!</p>
                    @endif
                    <hr>
                </div>
                <div class="mb-4">
                    <pre class="text-gray-800">{{ $selectedMessage->content }}</pre>
                </div>
                <div class="flex justify-between mb-4">
                    <button wire:click="toggleReplyForm"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        Reply
                    </button>
                    <button wire:click="deleteMessage"
                        class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                        Delete
                    </button>
                    @if ($selectedMessage->parent_id)
                        <button wire:click="togglePreviousMessages"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                            {{ $showPreviousMessages ? 'Hide' : 'Show' }} Previous Messages
                        </button>
                    @endif
                </div>

                @if ($showPreviousMessages && count($previousMessages) > 0)
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold mb-2">Previous Messages</h3>
                        @foreach ($previousMessages as $prevMessage)
                            <div class="border-b py-2">
                                <p class="text-sm text-gray-600">From: {{ $prevMessage->sender->name }}</p>
                                <p class="text-sm text-gray-600">Recieved at:
                                    {{ $prevMessage->sent_at->format('M d, Y H:i') }}</p>
                                <pre class="text-gray-800">{{ $prevMessage->content }}</pre>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($showReplyForm)
                    <form wire:submit.prevent="sendReply" class="mt-4">
                        <div class="mb-4">
                            <label for="replyTitle" class="block text-sm font-medium text-gray-700">Subject</label>
                            <input wire:model="replyTitle" type="text" id="replyTitle"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('replyTitle')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="replyInquiryType" class="block text-sm font-medium text-gray-700">Inquiry
                                Type</label>
                            <select wire:model="replyInquiryType" id="replyInquiryType"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select inquiry type</option>
                                <option value="general">General</option>
                                <option value="interview">Interview</option>
                                <option value="application">Application</option>
                            </select>
                            @error('replyInquiryType')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="replyContent" class="block text-sm font-medium text-gray-700">Reply</label>
                            <textarea wire:model="replyContent" id="replyContent" rows="4"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                            @error('replyContent')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Send Reply</button>
                    </form>
                @endif
            @elseif($messagePaginator->isNotEmpty())
                <p class="text-gray-600">Select a message to view.</p>
            @endif
        </div>
    </div>

    @if ($showNewMessageForm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" id="new-message-modal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">New Message</h3>
                    <form wire:submit.prevent="sendNewMessage" class="mt-2">
                        <div class="mb-4">
                            <label for="newMessageReceiver" class="block text-sm font-medium text-gray-700">To
                                (Email)</label>
                            <input wire:model="newMessageReceiver" type="email" id="newMessageReceiver"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('newMessageReceiver')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="newMessageTitle"
                                class="block text-sm font-medium text-gray-700">Subject</label>
                            <input wire:model="newMessageTitle" type="text" id="newMessageTitle"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('newMessageTitle')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="newMessageInquiryType" class="block text-sm font-medium text-gray-700">Inquiry
                                Type</label>
                            <select wire:model="newMessageInquiryType" id="newMessageInquiryType"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select inquiry type</option>
                                <option value="general">General</option>
                                <option value="interview">Interview</option>
                                <option value="application">Application</option>
                            </select>
                            @error('newMessageInquiryType')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="newMessageContent"
                                class="block text-sm font-medium text-gray-700">Message</label>
                            <textarea wire:model="newMessageContent" id="newMessageContent" rows="4"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                            @error('newMessageContent')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex justify-between">
                            <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Send
                                Message</button>
                            <button type="button" wire:click="toggleNewMessageForm"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <div
        class="text-center mt-6 sm:mt-12 w-full max-w-[506px] mx-auto px-2 sm:px-0">
        <a href="{{ url('/') }}"
            class="px-4 sm:px-6 py-2 sm:py-3 text-xs sm:text-sm bg-white rounded-md border border-black w-full sm:w-auto">
            Return to TOP
        </a>
    </div>
</div>
