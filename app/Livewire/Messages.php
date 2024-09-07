<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Message;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class Messages extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedMessage = null;
    public $replyContent = '';
    public $replyInquiryType = '';
    public $replyTitle = '';
    public $newMessageReceiver = '';
    public $newMessageTitle = '';
    public $newMessageContent = '';
    public $newMessageInquiryType = '';
    public $showReplyForm = false;
    public $showNewMessageForm = false;
    public $messageType = 'inbox'; // 'inbox' or 'sent'
    public $showPreviousMessages = false;
    public $previousMessages = [];

    protected $queryString = ['search', 'messageType'];

    protected $rules = [
        'replyContent' => 'required|min:10',
        'replyInquiryType' => 'required',
        'replyTitle' => 'required|min:3',
        'newMessageReceiver' => 'required|email|exists:users,email',
        'newMessageTitle' => 'required|min:3',
        'newMessageContent' => 'required|min:10',
        'newMessageInquiryType' => 'required',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $messagePaginator = $this->getMessages();

        return view('livewire.messages', [
            'messagePaginator' => $messagePaginator,
        ])->layout('layouts.app');
    }

    public function getMessages()
    {
        $query = $this->messageType === 'inbox'
            ? Message::where('receiver_user_id', Auth::id())
            : Message::where('sender_user_id', Auth::id());

        return $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('content', 'like', '%' . $this->search . '%');
            })
            ->orderBy('sent_at', 'desc')
            ->paginate(5);
    }

    public function selectMessage($id)
    {
        $this->selectedMessage = Message::findOrFail($id);
        $this->showReplyForm = false;
        $this->showPreviousMessages = false;
        $this->previousMessages = [];

        if ($this->messageType === 'inbox' && !$this->selectedMessage->read_at) {
            $this->selectedMessage->update(['read_at' => now()]);
        }
    }

    public function toggleReplyForm()
    {
        $this->showReplyForm = !$this->showReplyForm;
        $this->replyContent = '';
        $this->replyInquiryType = $this->selectedMessage->inquiry_type;
        $this->replyTitle =$this->selectedMessage->title;
    }

    public function sendReply()
    {
        $validator = Validator::make(
            [
                'replyContent' => $this->replyContent,
                'replyInquiryType' => $this->replyInquiryType,
                'replyTitle' => $this->replyTitle,
            ],
            [
                'replyContent' => 'required|min:10',
                'replyInquiryType' => 'required',
                'replyTitle' => 'required|min:3',
            ]
        );

        if ($validator->fails()) {
            $this->addError('replyContent', $validator->errors()->first('replyContent'));
            $this->addError('replyInquiryType', $validator->errors()->first('replyInquiryType'));
            $this->addError('replyTitle', $validator->errors()->first('replyTitle'));
            return;
        }

        Message::create([
            'sender_user_id' => Auth::id(),
            'sender_user_type' => Auth::user()->user_type,
            'receiver_user_id' => $this->selectedMessage->sender_user_id,
            'receiver_user_type' => $this->selectedMessage->sender_user_type,
            'sent_at' => now(),
            'title' => $this->replyTitle,
            'content' => $this->replyContent,
            'message_category' => 'Sent',
            'parent_id' => $this->selectedMessage->id,
            'inquiry_type' => $this->replyInquiryType,
        ]);

        $this->showReplyForm = false;
        $this->replyContent = '';
        $this->replyInquiryType = '';
        $this->replyTitle = '';
        $this->selectMessage($this->selectedMessage->id);

        flash()->success('Reply sent successfully!');
    }

    public function toggleNewMessageForm()
    {
        $this->showNewMessageForm = !$this->showNewMessageForm;
        $this->resetNewMessageForm();
    }

    public function sendNewMessage()
    {
        $validator = Validator::make(
            [
                'newMessageReceiver' => $this->newMessageReceiver,
                'newMessageTitle' => $this->newMessageTitle,
                'newMessageContent' => $this->newMessageContent,
                'newMessageInquiryType' => $this->newMessageInquiryType,
            ],
            [
                'newMessageReceiver' => 'required|email|exists:users,email',
                'newMessageTitle' => 'required|min:3',
                'newMessageContent' => 'required|min:10',
                'newMessageInquiryType' => 'required',
            ]
        );

        if ($validator->fails()) {
            $this->addError('newMessageReceiver', $validator->errors()->first('newMessageReceiver'));
            $this->addError('newMessageTitle', $validator->errors()->first('newMessageTitle'));
            $this->addError('newMessageContent', $validator->errors()->first('newMessageContent'));
            $this->addError('newMessageInquiryType', $validator->errors()->first('newMessageInquiryType'));
            return;
        }

        $receiver = User::where('email', $this->newMessageReceiver)->firstOrFail();

        Message::create([
            'sender_user_id' => Auth::id(),
            'sender_user_type' => Auth::user()->user_type,
            'receiver_user_id' => $receiver->id,
            'receiver_user_type' => $receiver->user_type,
            'sent_at' => now(),
            'title' => $this->newMessageTitle,
            'content' => $this->newMessageContent,
            'message_category' => 'Sent',
            'inquiry_type' => $this->newMessageInquiryType,
        ]);

        $this->showNewMessageForm = false;
        $this->resetNewMessageForm();

        flash()->success('Message sent successfully!');
    }

    public function resetNewMessageForm()
    {
        $this->newMessageReceiver = '';
        $this->newMessageTitle = '';
        $this->newMessageContent = '';
        $this->newMessageInquiryType = '';
    }

    public function deleteMessage()
    {
        if ($this->selectedMessage) {
            $this->selectedMessage->delete();
            $this->selectedMessage = null;

            flash()->success('Message deleted successfully!');
        }
    }

    public function togglePreviousMessages()
    {
        $this->showPreviousMessages = !$this->showPreviousMessages;
        if ($this->showPreviousMessages) {
            $this->loadPreviousMessages();
        }
    }

    public function loadPreviousMessages()
    {
        $this->previousMessages = [];
        $currentMessage = $this->selectedMessage;

        while ($currentMessage->parent_id) {
            $parentMessage = Message::find($currentMessage->parent_id);
            if ($parentMessage) {
                $this->previousMessages[] = $parentMessage;
                $currentMessage = $parentMessage;
            } else {
                break;
            }
        }

        $this->previousMessages = array_reverse($this->previousMessages);
    }

    public function switchMessageType($type)
    {
        $this->messageType = $type;
        $this->selectedMessage = null;
        $this->resetPage();
    }
}
