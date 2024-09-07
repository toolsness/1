<?php

namespace App\Livewire\Common;

use Livewire\Component;
use App\Models\Candidate;
use App\Models\Country;
use App\Models\QualificationCategory;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\DB;

class JobSeekerSearch extends Component
{
    use WithPagination;

    public $page;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $desiredIndustry = '';

    #[Url(history: true)]
    public $country = '';

    #[Url(history: true)]
    public $ageMin = 1;

    #[Url(history: true)]
    public $ageMax = 100;

    protected $queryString = ['search', 'desiredIndustry', 'country', 'ageMin', 'ageMax'];

    public function mount()
    {
        $this->fill(request()->only('search', 'desiredIndustry', 'country', 'ageMin', 'ageMax'));
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $candidates = Candidate::where('publish_category', 'Published')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('id', 'like', '%' . $this->search . '%')
                        ->orWhereRaw('CAST(TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) AS CHAR) LIKE ?', ['%' . $this->search . '%'])
                        ->orWhereHas('country', function ($subQuery) {
                            $subQuery->where('country_name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('qualificationRelation', function ($subQuery) {
                            $subQuery->where('qualification_name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('qualificationRelation.qualificationCategory', function ($subQuery) {
                            $subQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('student', function ($subQuery) {
                            $subQuery->where('name_kanji', 'like', '%' . $this->search . '%')
                                ->orWhere('name_katakana', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->desiredIndustry, function ($query) {
                $query->whereHas('qualificationRelation.qualificationCategory', function ($q) {
                    $q->where('name', $this->desiredIndustry);
                });
            })
            ->when($this->country, function ($query) {
                $query->whereHas('country', function ($q) {
                    $q->where('country_name', $this->country);
                });
            })
            ->when($this->ageMin && $this->ageMax, function ($query) {
                $query->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN ? AND ?', [$this->ageMin, $this->ageMax]);
            })
            ->paginate(5);

        $countries = Country::orderBy('country_name')->pluck('country_name');
        $industries = QualificationCategory::orderBy('name')->pluck('name');

        return view('livewire.common.job-seeker-search', [
            'candidates' => $candidates,
            'countries' => $countries,
            'industries' => $industries,
        ]);
    }

    public function viewCandidate($candidateId)
    {
        return redirect()->route('job-seeker.view', [
            'id' => $candidateId,
            'search' => $this->search,
            'desiredIndustry' => $this->desiredIndustry,
            'country' => $this->country,
            'ageMin' => $this->ageMin,
            'ageMax' => $this->ageMax,
            'page' => $this->page
        ]);
    }
}
