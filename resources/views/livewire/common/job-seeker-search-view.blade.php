<div class="px-4 py-12 bg-white sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="flex flex-col gap-8 lg:flex-row">
            <!-- Sidebar -->
            <!-- Sidebar -->
            <aside class="w-full lg:w-1/4">
                <div class="flex flex-col items-center text-center text-black">
                    <div class="bg-orange-600 text-white text-sm px-4 py-1.5 rounded-sm mb-4">
                        {{ ucfirst($candidate->qualificationRelation->qualificationCategory->name) }}:
                        {{ ucfirst($candidate->qualificationRelation->qualification_name) }}
                    </div>
                    <img src="{{ $candidate->profile_picture_link ? Storage::url($candidate->profile_picture_link) : 'https://via.placeholder.com/176' }}"
                        alt="Profile picture" class="h-auto mb-6 w-44" />
                    @if (Auth::user()->user_type !== 'Student' && Auth::user()->user_type !== 'Candidate')
                        <div class="w-full p-6 bg-neutral-100 rounded-xl border-5 border-sky-400">
                            <button wire:click="toggleScout"
                                class="w-full px-4 py-3 rounded-md shadow-sm text-xl mb-4 {{ $isScoutedByCurrentUser ? 'bg-green-500 text-white' : 'bg-white text-black' }} transition duration-300 hover:bg-opacity-90">
                                {{ $isScoutedByCurrentUser ? 'Scouting' : 'Scout' }}
                            </button>
                        </div>
                    @endif

                </div>
            </aside>
            {{-- <aside class="w-full lg:w-1/4">
                <div class="flex flex-col items-center text-center text-black">
                    <div class="bg-orange-600 text-white text-sm px-4 py-1.5 rounded-sm mb-4">
                        {{ ucfirst($candidate->qualificationRelation->qualificationCategory->name) }}:
                        {{ ucfirst($candidate->qualificationRelation->qualification_name) }}
                    </div>
                    <img src="{{ Storage::url($candidate->profile_picture_link) ?? 'https://via.placeholder.com/176' }}"
                        alt="Profile picture" class="h-auto mb-6 w-44" />
                    <div class="w-full p-6 bg-neutral-100 rounded-xl border-5 border-sky-400">
                        <div class="px-4 py-3 mb-4 text-xl bg-white rounded-md shadow-sm">
                            {{ $isScoutedByCurrentUser ? 'Scouted' : 'Scout' }}
                        </div>
                        <i wire:click="toggleScout"
                            class="fa-heart w-8 h-auto ml-auto cursor-pointer {{ $isScoutedByCurrentUser ? 'fas text-red-500' : 'far' }}"></i>
                    </div>
                </div>
            </aside> --}}

            <!-- Main Content -->
            <article class="w-full lg:w-3/4">
                <div class="text-sm black">
                    <dl class="space-y-6">
                        @php
                            $infoItems = [
                                'Registration No.' => $candidate->id,
                                'Name' => $candidate->name,
                                'Gender' => $candidate->gender,
                                'Date of Birth' => $candidate->birth_date->format('F j, Y'),
                                'Age' => $candidate->birth_date->age . ' years old',
                                'Country of origin' => $candidate->country->country_name,
                                'Academic background' => $candidate->last_education,
                                'work experience' => $candidate->work_history,
                                'Qualifications' => $candidate->qualification,
                                'Self-publicity' => $candidate->self_presentation,
                                'Person\'s wishes' => $candidate->personal_preference ?? 'None',
                                // 'CV' => '<a href="' . $candidate->cv_link . '">View CV</a>',
                            ];
                        @endphp

                        @foreach ($infoItems as $label => $value)
                            <div class="flex flex-col sm:flex-row">
                                <dt class="w-full pr-4 font-semibold text-right sm:w-1/3">{{ $label }}：</dt>
                                <dd class="w-full sm:w-2/3">
                                    @if ($label === 'CV')
                                        {!! $value !!}
                                    @else
                                        {{ nl2br(e($value)) }}
                                    @endif
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </article>
        </div>

        <!-- Navigation -->
        <nav class="flex flex-col justify-center gap-4 mt-12 sm:flex-row">
            <a href="{{ route('job-seeker.search') }}"
                class="px-6 py-3 text-sm text-center transition duration-300 bg-white border border-black rounded-md hover:bg-gray-100">
                Back to Search
            </a>
            <a href="{{ route('home') }}"
                class="px-6 py-3 text-sm text-center transition duration-300 bg-white border border-black rounded-md hover:bg-gray-100">
                Return to TOP
            </a>
        </nav>
    </div>
</div>
