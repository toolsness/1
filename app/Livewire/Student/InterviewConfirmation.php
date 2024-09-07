<?php

namespace App\Livewire\Student;

use Livewire\Component;
use App\Models\Interview;
use App\Enum\InterviewStatus;

class InterviewConfirmation extends Component
{
    public $interview;
    public $confirmationMessage;
    public $showCancelModal = false;
    public $cancelReason = '';

    protected $rules = [
        'cancelReason' => 'required|string|min:5',
    ];

    public function mount(Interview $interview)
    {
        $this->interview = $interview->load(['vacancy.companyRepresentative.company', 'inchargeUser']);
        $this->updateConfirmationMessage();
    }

    public function updateConfirmationMessage()
    {
        if ($this->interview->status === InterviewStatus::APPLICATION_FROM_STUDENTS) {
            $this->confirmationMessage = 'Your application has been submitted. Please wait for the company to confirm the interview schedule.';
        } elseif ($this->interview->status === InterviewStatus::INTERVIEW_CONFIRMED) {
            $this->confirmationMessage = 'Your interview has been confirmed. Please make note of the date and time.';
        } elseif ($this->interview->status === InterviewStatus::CANCELLATION_REFUSAL) {
            $this->confirmationMessage = 'Your interview has been cancelled.';
        } elseif ($this->interview->status === InterviewStatus::INTERVIEW_CANCELLED) {
            $this->confirmationMessage = 'Your interview has been cancelled.';
        } elseif ($this->interview->status === InterviewStatus::INTERVIEW_CONDUCTED) {
            $this->confirmationMessage = 'Your interview has been conducted. Please wait for the result.';
        } elseif ($this->interview->status === InterviewStatus::EMPLOYMENT_APPLICATION) {
            $this->confirmationMessage = 'Thank you for your employment application. We will proceed with the employment application with this job seeker. Our office will contact you within one business day.';
        } elseif ($this->interview->status === InterviewStatus::HIRED) {
            $this->confirmationMessage = 'Your application has been accepted. You have been hired by this company.';
        }
    }

    public function openCancelModal()
    {
        $this->showCancelModal = true;
    }

    public function cancelInterview()
    {
        $this->validate();

        $this->interview->update([
            'status' => InterviewStatus::CANCELLATION_REFUSAL,
            'reason' => $this->cancelReason,
        ]);

        $this->showCancelModal = false;
        $this->cancelReason = '';
        $this->interview->refresh();
        $this->updateConfirmationMessage();
    }

    public function render()
    {
        return view('livewire.student.interview-confirmation');
    }
}
