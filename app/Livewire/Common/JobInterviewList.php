<?php

namespace App\Livewire\Common;

use App\Enum\InterviewStatus;
use App\Enum\ReservationStatus;
use App\Models\Interview;
use App\Models\InterviewSchedule;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class JobInterviewList extends Component
{
    use WithPagination;

    public $sortField = 'implementation_date';
    public $sortDirection = 'desc';
    public $vacancyId;
    public $filterStatus = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $filterCompany = '';
    public $showFilters = false;
    public $showModal = false;
    public $modalType = '';
    public $selectedInterviewId;
    public $selectedInterview;
    public $reason = '';
    public $otherReason = '';
    public $showConfirmation = false;
    public $confirmationMessage = '';
    public $showScheduleModal = false;
    public $interviewDate;
    public $interviewTime;
    public $showArchived = false;


    protected $queryString = ['sortField', 'sortDirection'];

    public function mount($vacancyId = null)
    {
        $this->vacancyId = $vacancyId;

        if (Auth::user()->user_type === 'Student') {
            flash('success', 'You have to create your CV first!');
            return redirect()->route('student.candidate-details');
        }
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->filterStatus = '';
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
        $this->filterCompany = '';
        $this->resetPage();
    }

    public function openModal($type, $interviewId)
    {
        $this->modalType = $type;
        $this->selectedInterviewId = $interviewId;
        $this->selectedInterview = Interview::with(['vacancy', 'candidate'])->find($interviewId);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->modalType = '';
        $this->selectedInterviewId = null;
        $this->selectedInterview = null;
        $this->reason = '';
        $this->otherReason = '';
    }

    public function updatedReason($value)
    {
        if ($value !== 'Other (please specify)') {
            $this->otherReason = '';
        }
    }

    private function updateInterviewStatus($status)
    {
        $interview = Interview::find($this->selectedInterviewId);
        if ($interview) {
            $interview->status = $status;
            $interview->reason = $this->reason === 'Other (please specify)' ? $this->otherReason : $this->reason;
            $interview->save();
        }
        $this->closeModal();
    }

    public function confirmEmploymentApplication()
    {
        $this->updateInterviewStatus(InterviewStatus::EMPLOYMENT_APPLICATION);
        $this->showConfirmation = true;
        $this->confirmationMessage = 'Thank you for your employment application. We will proceed with the employment application with this job seeker. Our office will contact you within one business day.';
    }

    public function unofficialOffer()
    {
        $this->updateInterviewStatus(InterviewStatus::UNOFFICIAL_OFFER);
        $this->showConfirmation = true;
        $this->confirmationMessage = 'An unofficial offer has been made to this job seeker.';
    }

    public function withdrawOffer()
    {
        $this->updateInterviewStatus(InterviewStatus::OFFER_WITHDRAWN);
        $this->showConfirmation = true;
        $this->confirmationMessage = 'We have withdrawn the job offer for this job seeker.';
    }

    public function cancelInterview()
    {
        $this->updateInterviewStatus(InterviewStatus::CANCELLATION_REFUSAL);
        $this->showConfirmation = true;
        $this->confirmationMessage = Auth::user()->user_type === 'Candidate'
            ? 'You have cancelled the interview.'
            : 'We have cancelled the interview with this job seeker.';
    }

    public function refuseInterview()
    {
        $this->updateInterviewStatus(InterviewStatus::CANCELLATION_REFUSAL);
        $this->showConfirmation = true;
        $this->confirmationMessage = 'We have refused to interview this job seeker.';
    }

    public function markInterviewUnsuccessful()
    {
        $this->updateInterviewStatus(InterviewStatus::INTERVIEW_UNSUCCESSFUL);
        $this->showConfirmation = true;
        $this->confirmationMessage = 'The interview has been marked as unsuccessful.';
    }

    public function makeUnofficialOffer()
    {
        $this->updateInterviewStatus(InterviewStatus::UNOFFICIAL_OFFER);
        $this->showConfirmation = true;
        $this->confirmationMessage = 'An unofficial offer has been made to this job seeker.';
    }

    public function employmentApplication($interviewId = null)
    {
        $interview = Interview::findOrFail($interviewId);
        $this->updateInterviewStatus(InterviewStatus::EMPLOYMENT_APPLICATION);
        $this->showConfirmation = true;
        if(Auth::user()->user_type === 'Candidate'){
            $this->confirmationMessage = 'Thank you for your employment application. We will proceed with the employment application with this job seeker. Our office will contact you within one business day.';
        }
        else{
            $this->confirmationMessage = 'Congratulations! We will now proceed with your employment application with this company. Please wait for our office to contact you within one business day.';
        }
        $this->closeModal();
            return redirect()->route('student.interview.confirmation', ['interview' => $interviewId]);
    }

    public function declineOffer()
    {
        $this->updateInterviewStatus(InterviewStatus::OFFER_DECLINED);
        $this->showConfirmation = true;
        $this->confirmationMessage = 'You have declined the job offer.';
    }

    public function closeConfirmation()
    {
        $this->showConfirmation = false;
        $this->confirmationMessage = '';
        $this->reason = '';
        $this->otherReason = '';
    }

    public function saveInterviewSchedule()
    {
        $interview = Interview::find($this->selectedInterviewId);
        if ($interview) {
            $interviewSchedule = $interview->interviewSchedule ?? new InterviewSchedule;
            $interviewSchedule->vacancy_id = $interview->vacancy_id;
            $interviewSchedule->interview_date = $this->interviewDate;
            $interviewSchedule->interview_start_time = $this->interviewTime;
            $interviewSchedule->reservation_status = ReservationStatus::RESERVED;
            $interviewSchedule->save();

            $interview->interviewSchedule()->associate($interviewSchedule);
            $interview->status = InterviewStatus::INTERVIEW_CONFIRMED;
            $interview->save();

            $this->showScheduleModal = false;
            $this->showConfirmation = true;
            $this->confirmationMessage = "Interview schedule has been finalized and the status is set to 'Interview confirmed'.";

            $this->reset(['interviewDate', 'interviewTime']);
        }
    }

    private function refreshInterviews()
    {
        $user = Auth::user();
        $interviewsQuery = Interview::with(['vacancy.companyRepresentative.company', 'vacancy.companyAdmin.company', 'student', 'candidate', 'inchargeUser', 'interviewSchedule'])
            ->orderBy($this->sortField, $this->sortDirection);

        $companyId = $this->getUserCompanyId($user);

        if ($companyId) {
            if ($user->user_type === 'Candidate') {
                $interviewsQuery->where('candidate_id', $companyId);
            } else {
                $interviewsQuery->whereHas('vacancy', function ($query) use ($companyId) {
                    $query->where(function ($q) use ($companyId) {
                        $q->whereHas('companyRepresentative', function ($r) use ($companyId) {
                            $r->where('company_id', $companyId);
                        })->orWhereHas('companyAdmin', function ($r) use ($companyId) {
                            $r->where('company_id', $companyId);
                        });
                    });
                });
            }
        }

        if ($this->vacancyId) {
            $interviewsQuery->where('vacancy_id', $this->vacancyId);
        }

        if ($this->filterStatus) {
            $interviewsQuery->where('status', $this->filterStatus);
        }

        if ($this->filterDateFrom) {
            $interviewsQuery->whereDate('implementation_date', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $interviewsQuery->whereDate('implementation_date', '<=', $this->filterDateTo);
        }

        if ($this->filterCompany) {
            $interviewsQuery->whereHas('vacancy', function ($query) {
                $query->whereHas('companyRepresentative.company', function ($q) {
                    $q->where('name', 'like', '%'.$this->filterCompany.'%');
                })->orWhereHas('companyAdmin.company', function ($q) {
                    $q->where('name', 'like', '%'.$this->filterCompany.'%');
                });
            });
        }

        if ($this->showArchived) {
            $interviewsQuery->where('status', InterviewStatus::ARCHIVED);
        } else {
            $interviewsQuery->where('status', '!=', InterviewStatus::ARCHIVED);
        }

        return $interviewsQuery->paginate(5);
    }

    private function getUserCompanyId($user)
    {
        switch ($user->user_type) {
            case 'CompanyRepresentative':
                return $user->companyRepresentative->company_id ?? null;
            case 'CompanyAdmin':
                return $user->companyAdmin->company_id ?? null;
            case 'BusinessOperator':
                return $user->businessOperator->company_id ?? null;
            case 'Candidate':
                $student = $user->student;
                if ($student) {
                    return $student->candidate->id ?? null;
                }
                return null;
            default:
                return null;
        }
    }

    public function archiveInterview($interviewId)
    {
        $interview = Interview::find($interviewId);
        if ($interview) {
            $interview->status = InterviewStatus::ARCHIVED;
            $interview->save();
        }
        $this->closeModal();
    }

    public function deleteInterview($interviewId)
    {
        $interview = Interview::find($interviewId);
        if ($interview && $interview->status === InterviewStatus::ARCHIVED) {
            $interview->delete();
        }
        $this->closeModal();
    }

    public function toggleShowArchived()
    {
        $this->showArchived = !$this->showArchived;
        $this->resetPage();
    }

    public function unsuccessfulInterview()
    {
        $this->updateInterviewStatus(InterviewStatus::INTERVIEW_UNSUCCESSFUL);
        $this->showConfirmation = true;
        $this->confirmationMessage = 'The interview has been marked as unsuccessful.';
    }

    public function hire($interviewId)
    {
        $interview = Interview::findOrFail($interviewId);
        if ($interview->status === InterviewStatus::EMPLOYMENT_APPLICATION) {
            $interview->status = InterviewStatus::HIRED;
            $interview->save();
            $this->showConfirmation = true;
            $this->confirmationMessage = 'The candidate has been successfully hired.';
        }
    }

    public function approveScout($interviewId)
    {
        $interview = Interview::findOrFail($interviewId);
        if ($interview->status === InterviewStatus::SCOUTED) {
            $this->closeModal();
            return redirect()->route('student.candidate-details', ['vacancyId' => $interview->vacancy_id, 'interviewId' => $interviewId]);
        }
    }

    public function declineScout($interviewId)
    {
        $interview = Interview::findOrFail($interviewId);
        if ($interview->status === InterviewStatus::SCOUTED) {
            $interview->status = InterviewStatus::OFFER_DECLINED;
            $interview->reason = $this->reason === 'Other (please specify)' ? $this->otherReason : $this->reason;
            $interview->save();
            $this->closeModal();
            $this->showConfirmation = true;
            $this->confirmationMessage = 'You have declined the scouting request.';
        }
    }

    public function cancelScout($interviewId)
    {
        $interview = Interview::findOrFail($interviewId);
        if ($interview->status === InterviewStatus::SCOUTED) {
            $interview->status = InterviewStatus::CANCELLATION_REFUSAL;
            $interview->reason = $this->reason === 'Other (please specify)' ? $this->otherReason : $this->reason;
            $interview->save();
            $this->closeModal();
            $this->showConfirmation = true;
            $this->confirmationMessage = 'You have cancelled the scouting request.';
        }
    }

    public function finalizeInterviewSchedule($interviewId)
    {
        $interview = Interview::findOrFail($interviewId);
        if ($interview->status === InterviewStatus::APPLICATION_FROM_STUDENTS) {
            $interview->status = InterviewStatus::INTERVIEW_CONFIRMED;
            $interview->save();

            InterviewSchedule::create([
                'vacancy_id' => $interview->vacancy_id,
                'interview_date' => $interview->implementation_date,
                'interview_start_time' => $interview->implementation_start_time,
                'reservation_status' => ReservationStatus::RESERVED,
            ]);

            $this->showConfirmation = true;
            $this->confirmationMessage = 'Interview schedule has been finalized.';
        }
    }

    public function render()
    {
        $user = Auth::user();
        $interviews = $this->refreshInterviews();
        $statuses = array_filter(InterviewStatus::cases(), function ($status) {
            return $status !== InterviewStatus::ARCHIVED;
        });

        return view('livewire.common.job-interview-list', [
            'interviews' => $interviews,
            'interviewDetailsRoute' => 'interview.details',
            'statuses' => $statuses,
        ]);
    }
}
