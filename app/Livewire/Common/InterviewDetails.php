<?php

namespace App\Livewire\Common;

use Livewire\Component;
use App\Models\Interview;
use App\Models\InterviewMemo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class InterviewDetails extends Component
{
    public $interview;
    public $activeTab = 'interview';
    public $memoContent = '';

    protected $rules = [
        'memoContent' => 'required|string|max:1000',
    ];

    public function mount(Interview $interview)
    {
        $this->interview = $interview->load('interviewSchedule', 'vacancy.companyRepresentative', 'student.candidate', 'memos.user', 'inchargeUser');
    }

    public function returnToInterviewList()
    {
        return redirect()->route('job-interviews', ['vacancyId' => $this->interview->vacancy_id]);
    }

    public function saveMemo()
    {
        $this->validate();

        $memo = new InterviewMemo([
            'interview_id' => $this->interview->id,
            'user_id' => Auth::id(),
            'content' => $this->memoContent,
        ]);

        $memo->save();

        $this->interview->refresh();
        $this->memoContent = '';
        $this->dispatch('memoSaved'); // Changed from emit to dispatch
    }

    public function render()
    {
        return view('livewire.common.interview-details');
    }
}
