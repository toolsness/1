<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserStudentCandidateSeeder extends Seeder
{
    public function run()
    {
        User::factory()
            ->count(15)
            ->has(Student::factory()
                ->has(Candidate::factory())
            )
            ->create();
    }
}