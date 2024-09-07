<?php

namespace App\Livewire\Student;

use App\Models\Candidate;
use App\Models\Country;
use App\Models\Interview;
use App\Models\Qualification;
use App\Models\Vacancy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CandidateDetails extends Component
{
    use WithFileUploads;

    public $user;

    public $student;

    public $candidate;

    public $vacancy;

    public $editing = false;

    public $confirmingApplication = false;

    public $qualifications;

    public $countries;

    public $profilePicture;

    public $tempProfilePicture;

    public $interview;

    public $isLoading = false;

    public $isUploading = false;

    protected $rules = [
        'candidate.name' => 'required|string|max:255',
        'candidate.gender' => 'required|in:Male,Female,Other',
        'candidate.birth_date' => 'required|date|before:today',
        'candidate.nationality' => 'required|exists:countries,id',
        'candidate.last_education' => 'required|string',
        'candidate.work_history' => 'nullable|string',
        'candidate.qualification' => 'required|exists:qualifications,id',
        'candidate.self_presentation' => 'nullable|string',
        'candidate.personal_preference' => 'nullable|string',
        'profilePicture' => 'nullable|image|max:1024', // 1MB Max
        'candidate.publish_category' => 'required|in:NotPublished,Published,PublicationStopped',
    ];

    public function mount($vacancyId = null, $interviewId = null)
    {
        $this->user = Auth::user();
        $this->student = $this->user->student;
        $this->candidate = $this->student->candidate ?? new Candidate;
        $this->vacancy = $vacancyId ? Vacancy::findOrFail($vacancyId) : null;
        $this->interview = $interviewId ? Interview::findOrFail($interviewId) : null;
        $this->confirmingApplication = ($this->vacancy || $this->interview) && $this->candidate->exists;
        $this->qualifications = Qualification::all();
        $this->countries = Country::orderBy('country_name')->get();

        if (! $this->candidate->exists) {
            $this->candidate->fill([
                'name' => $this->user->name ?? '',
                'gender' => '',
                'birth_date' => null,
                'nationality' => null,
                'self_presentation' => '',
                'personal_preference' => '',
                'publish_category' => 'NotPublished',
            ]);
            $this->editing = true;
        }
    }

    public function toggleEdit()
    {
        $this->isLoading = true;
        $this->editing = ! $this->editing;
        $this->isLoading = true;
    }

    public function updatedProfilePicture()
    {
        $this->isUploading = true;
        $this->validate([
            'profilePicture' => 'image|max:1024', // 1MB Max
        ]);

        try {
            $this->tempProfilePicture = $this->profilePicture->temporaryUrl();
        } finally {
            $this->isUploading = false;
        }
    }

    public function save()
    {
        $this->isLoading = true;
        $this->validate();

        try {
            Log::info('Saving candidate details', ['candidate' => $this->candidate->toArray()]);

            if (! $this->candidate->exists) {
                $this->candidate->student_id = $this->student->id;
                $this->candidate->created_by = $this->user->id;
            }

            $this->candidate->updated_by = $this->user->id;

            if ($this->profilePicture) {
                $path = $this->profilePicture->store('candidate-pictures', 's3');
                $this->candidate->profile_picture_link = $path;
            }

            $this->candidate->save();

            if ($this->user->user_type === 'Student') {
                $this->user->update(['user_type' => 'Candidate']);
                $this->user->refresh();
            }

            $this->editing = false;

            if ($this->vacancy && $this->confirmingApplication) {
                return redirect()->route('student.interview-application-time-selection', ['vacancyId' => $this->vacancy->id]);
            } elseif ($this->interview && $this->confirmingApplication) {
                return redirect()->route('student.interview-application-time-selection', [
                    'interviewId' => $this->interview->id,
                    'vacancyId' => $this->interview->vacancy_id,
                ]);
            }

            // Dispatch an event instead of emitting
            $this->dispatch('candidate-saved');

            // Add a flash message here
            session()->flash('message', 'Candidate details saved successfully.');

        } catch (\Exception $e) {
            Log::error('Error saving candidate details', ['error' => $e->getMessage()]);
            session()->flash('error', 'Error saving candidate details: '.$e->getMessage());
        } finally {
            $this->isLoading = true;
        }
    }

    public function removeProfilePicture()
    {
        if ($this->candidate->profile_picture_link) {
            Storage::disk('s3')->delete($this->candidate->profile_picture_link);
            $this->candidate->profile_picture_link = null;
            $this->candidate->save();
        }

        $this->profilePicture = null;
        $this->tempProfilePicture = null;
    }

    public function confirmApplication()
    {
        $this->isLoading = true;
        if (! $this->candidate->exists) {
            flash()->warning('Please save your details before confirming.');
            $this->isLoading = false;

            return;
        }

        if ($this->interview) {
            $this->isLoading = true;

            return redirect()->route('student.interview-application-time-selection', [
                'interviewId' => $this->interview->id,
                'vacancyId' => $this->interview->vacancy_id,
            ]);
        } elseif ($this->vacancy) {
            $this->isLoading = true;

            return redirect()->route('student.interview-application-time-selection', ['vacancyId' => $this->vacancy->id]);
        }

        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.student.candidate-details');
    }
}
