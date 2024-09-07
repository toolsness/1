<?php

namespace App\Livewire\Common;

use Livewire\Component;
use App\Models\Candidate;
use App\Models\Vacancy;
use App\Models\Interview;
use App\Enum\InterviewStatus;

class ConfirmScouting extends Component
{
    public $candidateId;
    public $jobId;
    public $candidate;
    public $job;

    public function mount($candidateId, $jobId)
    {
        $this->candidateId = $candidateId;
        $this->jobId = $jobId;
        $this->candidate = Candidate::findOrFail($candidateId);
        $this->job = Vacancy::findOrFail($jobId);
    }

    public function confirmScouting()
    {
        $interview = new Interview();
        $interview->candidate_id = $this->candidateId;
        $interview->vacancy_id = $this->jobId;
        $interview->status = InterviewStatus::SCOUTED;
        $interview->save();

        return redirect()->route('job-seeker.view', ['id' => $this->candidateId], flash()->success('Candidate has been Scouted.'));

    }

    public function cancelScouting()
    {
        return redirect()->route('job-seeker.view', ['id' => $this->candidateId], flash()->info('Scouting has been Cancelled.'));
    }

    public function render()
    {
        return view('livewire.common.confirm-scouting');
    }
}
