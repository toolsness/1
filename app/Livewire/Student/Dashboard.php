<?php

namespace App\Livewire\Student;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Candidate;

class Dashboard extends Component
{
    public function startJobHunting()
    {
        // if auth user is a student the we will change his user_type to Candidate and also create a candidte profile referencing his student id and set his candidate name according to the user name
            // we have to check that if there is already a candidate details for this user $user->student->id; available in the candidate table if exit we need to update the name and chage user type to the student else we need to create a new candidate profile


        if (Auth::user()->user_type == 'Student' && !Auth::user()->student->candidate) {
            $user = User::find(Auth::user()->id);
            $user->user_type = 'Candidate';
            $user->save();

            $candidate = new Candidate();
            $candidate->student_id = $user->student->id;
            $candidate->publish_category = 'NotPublished';
            $candidate->name = $user->name;
            $candidate->save();

        } elseif (Auth::user()->user_type == 'Student' && Auth::user()->student->candidate) {
            $user = User::find(Auth::user()->id);
            $user->user_type = 'Candidate';
            $user->save();

            $candidate = Candidate::find(Auth::user()->student->candidate->id);
            $candidate->name = $user->name;
            $candidate->save();
        }

        return redirect()->route('home');

    }

    public function render()
    {
        $tasks = [
            ['name' => 'Orientation', 'percentage' => 100],
            ['name' => 'Profile Registration', 'percentage' => 30],
            ['name' => 'Interview Response Practice', 'percentage' => 20],
            ['name' => 'Mock Interview', 'percentage' => 0],
        ];

        return view('livewire.student.dashboard', compact('tasks'));
    }
}
