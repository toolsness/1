<?php

namespace App\Livewire\Common;

use App\Models\Vacancy;
use App\Models\VacancyCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateJobListing extends Component
{
    use WithFileUploads;

    public $companyName;

    public $job_title;

    public $monthly_salary;

    public $work_location;

    public $working_hours;

    public $transportation_expenses;

    public $overtime_pay;

    public $salary_increase_and_bonuses;

    public $social_insurance;

    public $japanese_language;

    public $other_details;

    public $publish_category = 'NotPublished';

    public $image;

    public $vacancy_category_id;

    protected $rules = [
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
        'image' => 'nullable|image|max:1024', // 1MB Max
        'vacancy_category_id' => 'required|exists:vacancy_categories,id',
        // 'job_title' => 'required',
    ];

    public function mount()
    {
        $user = Auth::user();

        if ($user->user_type === 'CompanyAdmin') {
            $this->companyName = $user->companyAdmin->company->name;
        } elseif ($user->user_type === 'CompanyRepresentative') {
            $this->companyName = $user->companyRepresentative->company->name;
        } else {
            // Handle unexpected user type
            abort(403, 'Unauthorized action.');
        }
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();
        $vacancy = new Vacancy();

        if ($user->user_type === 'CompanyAdmin') {
            $vacancy->company_admin_id = $user->companyAdmin->id;
        } elseif ($user->user_type === 'CompanyRepresentative') {
            $vacancy->company_representative_id = $user->companyRepresentative->id;
        } else {
            // Handle unexpected user type
            abort(403, 'Unauthorized action.');
        }

        $vacancy->job_title = $this->job_title;
        $vacancy->monthly_salary = $this->monthly_salary;
        $vacancy->work_location = $this->work_location;
        $vacancy->working_hours = $this->working_hours;
        $vacancy->transportation_expenses = $this->transportation_expenses;
        $vacancy->overtime_pay = $this->overtime_pay;
        $vacancy->salary_increase_and_bonuses = $this->salary_increase_and_bonuses;
        $vacancy->social_insurance = $this->social_insurance;
        $vacancy->japanese_language = $this->japanese_language;
        $vacancy->other_details = $this->other_details;
        $vacancy->publish_category = $this->publish_category;
        $vacancy->vacancy_category_id = $this->vacancy_category_id;
        // $vacancy->job_title = $this->job_title;

        if ($this->image) {
            $imagePath = $this->image->store('vacancy_images', 's3');
            $vacancy->image = $imagePath;
        }

        $vacancy->save();

        flash()->success('Job listing created successfully!');

        return redirect()->route('job-list.search');
    }

    public function render()
    {
        $vacancyCategories = VacancyCategory::all();

        return view('livewire.common.create-job-listing', compact('vacancyCategories'));
    }
}
