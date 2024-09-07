<?php

namespace App\Livewire\Company\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\CompanyRepresentative;
use Illuminate\Support\Facades\Mail;
use App\Mail\RepresentativeApproved;
use App\Mail\RepresentativeRejected;
use Illuminate\Support\Facades\Auth;

class ApproveRepresentatives extends Component
{
    public $pendingRepresentatives;

    public function mount()
    {
        $this->loadPendingRepresentatives();
    }

    public function loadPendingRepresentatives()
    {
        $companyAdmin = Auth::user()->companyAdmin;

        if (!$companyAdmin) {
            flash()->error('You do not have permission to view this page.');
            return redirect()->route('home');
        }

        $this->pendingRepresentatives = User::where('user_type', 'CompanyRepresentative')
            ->where('login_permission_category', 'Pending')
            ->whereHas('companyRepresentative', function ($query) use ($companyAdmin) {
                $query->where('company_id', $companyAdmin->company_id);
            })
            ->with(['companyRepresentative.company' => function ($query) {
                $query->withDefault(['name' => 'No Company Assigned']);
            }])
            ->get();
    }

    public function approve($userId)
    {
        $user = $this->findAndValidateUser($userId);
        if (!$user) return;

        $user->login_permission_category = 'Allowed';
        $user->save();

        Mail::to($user->email)->send(new RepresentativeApproved($user));

        $this->loadPendingRepresentatives();
        flash()->success('Representative approved successfully.');
    }

    public function reject($userId)
    {
        $user = $this->findAndValidateUser($userId);
        if (!$user) return;

        $user->login_permission_category = 'NotAllowed';
        $user->save();

        Mail::to($user->email)->send(new RepresentativeRejected($user));

        $this->loadPendingRepresentatives();
        flash()->success('Representative rejected successfully.');
    }

    private function findAndValidateUser($userId)
    {
        $companyAdmin = Auth::user()->companyAdmin;
        $user = User::findOrFail($userId);

        if (!$user->companyRepresentative || $user->companyRepresentative->company_id !== $companyAdmin->company_id) {
            flash()->error('You do not have permission to manage this representative.');
            return null;
        }

        return $user;
    }

    public function render()
    {
        return view('livewire.company.admin.approve-representatives');
    }
}
