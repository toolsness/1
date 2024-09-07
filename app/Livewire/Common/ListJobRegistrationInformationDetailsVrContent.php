<?php

namespace App\Livewire\Common;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Vacancy;
use App\Models\VRContent;
use Illuminate\Support\Facades\Storage;

class ListJobRegistrationInformationDetailsVrContent extends Component
{
    use WithFileUploads;

    public $vacancy;
    public $vrContent;
    public $contentType;
    public $isEditing = false;
    public $newContentLink;
    public $newImage;

    public function mount($vacancyId, $contentType)
    {
        $this->vacancy = Vacancy::findOrFail($vacancyId);
        $this->contentType = $contentType;

        if ($contentType === 'Company Introduction') {
            $this->vrContent = $this->vacancy->vrContentCompanyIntroduction;
        } elseif ($contentType === 'Workplace Tour') {
            $this->vrContent = $this->vacancy->vrContentWorkplaceTour;
        }

        if (!$this->vrContent) {
            $this->vrContent = new VRContent([
                'content_name' => 'Default VR Content',
                'content_link' => 'https://example.com/default-vr-content',
                'image' => 'default-vr-image.jpg',
            ]);
        }

        $this->newContentLink = $this->vrContent->content_link;
    }

    public function toggleEdit()
    {
        $this->isEditing = !$this->isEditing;
        if (!$this->isEditing) {
            $this->newContentLink = $this->vrContent->content_link;
            $this->newImage = null;
        }
    }

    public function save()
    {
        $this->validate([
            'newContentLink' => 'required|url',
            'newImage' => 'nullable|image|max:1024', // 1MB Max
        ]);

        try {
            if (!$this->vrContent->id) {
                $this->vrContent = new VRContent();
            }

            if ($this->newImage) {
                if ($this->vrContent->image && $this->vrContent->image !== 'default-vr-image.jpg') {
                    Storage::delete('public/' . $this->vrContent->image);
                }
                $imagePath = $this->newImage->store('vr-images', 'public');
                $this->vrContent->image = str_replace('public/', '', $imagePath);
            }

            $this->vrContent->content_link = $this->newContentLink;
            $this->vrContent->content_name = $this->contentType . ' VR Content';
            $this->vrContent->save();

            if ($this->contentType === 'Company Introduction') {
                $this->vacancy->vr_content_company_introduction_id = $this->vrContent->id;
            } elseif ($this->contentType === 'Workplace Tour') {
                $this->vacancy->vr_content_workplace_tour_id = $this->vrContent->id;
            }
            $this->vacancy->save();

            $this->isEditing = false;
            session()->flash('message', 'VR content updated successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving VR content: ' . $e->getMessage());
        }
    }

    public function playVR()
    {
        return redirect()->away($this->vrContent->content_link);
    }

    public function render()
    {
        return view('livewire.common.list-job-registration-information-details-vr-content');
    }
}