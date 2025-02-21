<?php

namespace App\Livewire\Common;

use App\Models\Candidate;
use App\Models\Interview;
use App\Models\Vacancy;
use App\Models\VacancyCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class ListJobRegistrationInformationDetails extends Component
{
    use WithFileUploads;
    #[Validate('nullable|image|max:1024')] // 1MB Max

    public $vacancy;

    public $companyName;

    public $job_title;

    public $monthly_salary;

    public $work_location;

    public $working_hours;

    public $transportation_expenses;

    public $overtime_pay;

    public $salary_increase_and_bonuses;

    public $social_insurance;

    public $other_details;

    public $publish_category;

    public $newImage;

    public $japanese_language;

    public $vacancy_category_id;

    public $editing = false;

    public $editableFields = [
        'job_title', 'monthly_salary', 'work_location', 'working_hours',
        'transportation_expenses', 'overtime_pay', 'salary_increase_and_bonuses',
        'social_insurance', 'other_details', 'publish_category', 'japanese_language',
        'vacancy_category_id',
    ];

    public $userType;

    public $canApply = false;

    public $isInterviewDetailsPage = false;

    public function mount($id)
    {
        $this->isInterviewDetailsPage = Str::startsWith(request()->path(), 'interview/');

        $this->vacancy = Vacancy::with(['companyAdmin.company', 'companyRepresentative.company', 'vacancyCategory'])->find($id);
        if ($this->vacancy) {
            $this->loadVacancyData();
        }

        $this->userType = Auth::user()->user_type;
        $this->checkApplicationEligibility();
    }

    protected function loadVacancyData()
    {
        $company = $this->getCompany();
        $this->companyName = $company ? $company->name : 'N/A';
        $this->job_title = $this->vacancy->job_title;
        $this->monthly_salary = $this->vacancy->monthly_salary;
        $this->work_location = $this->vacancy->work_location;
        $this->working_hours = $this->vacancy->working_hours;
        $this->transportation_expenses = $this->vacancy->transportation_expenses;
        $this->overtime_pay = $this->vacancy->overtime_pay;
        $this->salary_increase_and_bonuses = $this->vacancy->salary_increase_and_bonuses;
        $this->social_insurance = $this->vacancy->social_insurance;
        $this->japanese_language = $this->vacancy->japanese_language;
        $this->other_details = $this->vacancy->other_details;
        $this->publish_category = $this->vacancy->publish_category;
        $this->vacancy_category_id = $this->vacancy->vacancy_category_id;
    }

    protected function getCompany()
    {
        if ($this->vacancy->companyAdmin) {
            return $this->vacancy->companyAdmin->company;
        } elseif ($this->vacancy->companyRepresentative) {
            return $this->vacancy->companyRepresentative->company;
        }

        return null;
    }

    protected function checkApplicationEligibility()
    {
        if ($this->userType === 'Student') {
            $user = Auth::user();
            $this->canApply = Candidate::where('student_id', $user->student->id)->exists();
        }
    }

    public function toggleEditing()
    {
        if (in_array($this->userType, ['CompanyAdmin', 'CompanyRepresentative'])) {
            $this->editing = ! $this->editing;
        }
    }

    public function cancelEditing()
    {
        $this->editing = false;
        $this->resetValidation();
        $this->newImage = null;
        $this->loadVacancyData(); // Reload the original data
    }

    public function save()
    {
        $this->validate([
            'job_title' => 'required',
            'monthly_salary' => 'required',
            'work_location' => 'required',
            'working_hours' => 'required',
            'transportation_expenses' => 'required',
            'overtime_pay' => 'required',
            'salary_increase_and_bonuses' => 'required',
            'social_insurance' => 'required',
            'japanese_language' => 'required',
            'other_details' => 'nullable',
            'publish_category' => 'required',
            'newImage' => 'nullable|image|max:1024', // 1MB Max
            'vacancy_category_id' => 'required|exists:vacancy_categories,id',
        ]);

        if ($this->vacancy) {
            $this->vacancy->update([
                'job_title' => $this->job_title,
                'monthly_salary' => $this->monthly_salary,
                'work_location' => $this->work_location,
                'working_hours' => $this->working_hours,
                'transportation_expenses' => $this->transportation_expenses,
                'overtime_pay' => $this->overtime_pay,
                'salary_increase_and_bonuses' => $this->salary_increase_and_bonuses,
                'social_insurance' => $this->social_insurance,
                'japanese_language' => $this->japanese_language,
                'other_details' => $this->other_details,
                'publish_category' => $this->publish_category,
                'vacancy_category_id' => $this->vacancy_category_id,
            ]);

            if ($this->newImage) {
                Log::info('Attempting to upload new image');
                try {
                    $imagePath = $this->newImage->store('vacancy_images', 's3');
                    Log::info('Image uploaded successfully: '.$imagePath);
                    $this->vacancy->update(['image' => $imagePath]);
                } catch (\Exception $e) {
                    Log::error('Failed to upload image: '.$e->getMessage());
                    flash()->error('Failed to upload image: '.$e->getMessage());
                }
            }

            flash()->success('Update Successful!');
        }

        $this->editing = false;
    }

    public function applyForInterview()
    {
        $user = Auth::user();

        if ($user->user_type === 'Student') {
            // Redirect to CandidateDetails to create a new candidate profile
            return redirect()->route('student.candidate-details', ['vacancyId' => $this->vacancy->id]);
        } elseif ($user->user_type === 'Candidate') {
            // Redirect directly to interview time selection
            return redirect()->route('student.candidate-details', ['vacancyId' => $this->vacancy->id]);
        } else {
            // Handle other user types or show an error
            session()->flash('error', 'You are not authorized to apply for interviews.');

            return null;
        }
    }

    public function getImageUrl($path)
    {
        if (! $path) {
            return asset('placeholder2.png');
        }

        return Storage::url($path);
    }

    public function render()
    {
        $vacancyCategories = VacancyCategory::all();
        $imageUrl = $this->vacancy && $this->vacancy->image
            ? $this->getImageUrl($this->vacancy->image)
            : asset('placeholder2.png');

        return view('livewire.common.list-job-registration-information-details', compact('vacancyCategories', 'imageUrl'));
    }
}
