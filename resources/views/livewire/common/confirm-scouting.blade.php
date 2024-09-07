<div class="bg-white py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-2xl font-semibold text-center mb-8">Confirm the job description and the job seekers you are
            scouting for</h1>

        <div class="flex flex-col md:flex-row justify-center items-center gap-8">
            <!-- Job Card -->
            <div class="w-full md:w-1/2 bg-white rounded-lg border border-black overflow-hidden shadow-sm">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4">{{ $job->title }}</h2>
                    <p class="mb-2"><strong>Business Type:</strong> {{ $job->job_title }}</p>
                    <p class="mb-2"><strong>Salary:</strong> {{ $job->monthly_salary }} ¥</p>
                    <p class="mb-2"><strong>Location:</strong> {{ $job->work_location }}</p>
                    <p><strong>Japanese Level:</strong> {{ $job->japanese_language }}</p>
                </div>
            </div>

            <!-- Arrow Icon -->
            <div class="text-4xl text-gray-400">
                <i class="fas fa-arrow-right"></i>
            </div>

            <!-- Candidate Card -->
            <div class="w-full md:w-1/2 bg-white rounded-lg border border-black overflow-hidden shadow-sm">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4">{{ $candidate->name }}</h2>
                    <p class="mb-2"><strong>Age:</strong> {{ $candidate->birth_date->age }} years old</p>
                    <p class="mb-2"><strong>Gender:</strong> {{ $candidate->gender }}</p>
                    <p class="mb-2"><strong>Country:</strong> {{ $candidate->country->country_name }}</p>
                    <p><strong>Education:</strong> {{ $candidate->last_education }}</p>
                </div>
            </div>
        </div>

        <div class="mt-12 text-center">
            <p class="text-lg mb-6">Would you like to scout for candidates here for this position?</p>
            <button wire:click="confirmScouting"
                class="px-8 py-3 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700 transition duration-300 mr-4">
                Confirm Scouting
            </button>
            <button wire:click="cancelScouting"
                class="px-8 py-3 text-sm text-black bg-white rounded-md border border-black hover:bg-gray-100 transition duration-300">
                Cancel
            </button>
        </div>
    </div>
</div>
