<div>
    <section class="flex items-center justify-center px-16 py-5 bg-white max-md:px-5">
        <div class="flex flex-col mt-5 w-full max-w-[945px] max-md:mt-10 max-md:max-w-full">
            <article
                class="shadow-lg flex flex-col pb-14 rounded-2xl border border-black border-solid max-md:max-w-full
    {{ $activeTab === 'resume' ? 'bg-sky-200' : ($activeTab === 'job' ? 'bg-orange-200' : 'bg-zinc-100') }}">
                <nav class="flex overflow-hidden text-xl text-center text-black">
                    <a href="#" wire:click="$set('activeTab', 'resume')"
                        class="flex-1 px-6 py-3 border-t border-l border-r relative
           {{ $activeTab === 'resume' ? 'bg-sky-200 font-bold rounded-tr-2xl rounded-tl-2xl' : 'bg-sky-200 rounded-tl-2xl rounded-tr-2xl opacity-90' }}">
                        <span class="relative z-10">Resumes of job seekers</span>
                    </a>
                    <a href="#" wire:click="$set('activeTab', 'job')"
                        class="flex-1 px-4 py-3 border-t border-r relative
           {{ $activeTab === 'job' ? 'bg-orange-200 font-bold rounded-tr-2xl' : 'bg-orange-200 rounded-tl-2xl rounded-tr-2xl opacity-90' }}">
                        <span class="relative z-10">Job Description Details</span>
                    </a>
                    <a href="#" wire:click="$set('activeTab', 'interview')"
                        class="flex-1 px-6 py-3 border-t border-r relative
           {{ $activeTab === 'interview'
               ? 'bg-zinc-100 font-bold rounded-tr-2xl'
               : 'bg-zinc-100 rounded-tl-2xl rounded-tr-2xl opacity-90' }}">
                        <span class="relative z-10">Interview Details</span>
                    </a>
                </nav>
                <div
                    class="flex justify-center items-center self-center px-16 py-20 mt-6 max-w-full bg-white rounded-2xl w-[854px] max-md:px-5">
                    @if ($activeTab === 'resume')
                        @if ($interview->candidate->id)
                            @livewire('common.job-seeker-search-view', ['id' => $interview->candidate->id])
                        @else
                            <p>Candidate data not found.</p>
                        @endif
                    @elseif ($activeTab === 'job')
                        @livewire('common.list-job-registration-information-details', ['id' => $interview->vacancy->id])
                    @elseif ($activeTab === 'interview')
                        <div class="flex flex-col mt-2 mb-24 max-w-full w-[627px] max-md:mb-10">
                            <div class="flex gap-5 max-w-full w-[627px] max-md:flex-col max-md:gap-0">
                                <div class="w-[41%] h-[130px] max-md:w-full">

                                        <h3 class=" mb-2 py-2 w-[30%] text-sm text-center bg-white border rounded-md border-black">Status</h3>
                                        <p class="px-2 py-6 text-xs font-bold text-center break-words border border-black rounded-md">
                                            {{ Auth::user()->user_type === 'Candidate' ? $interview->status->getDisplayText() : $interview->status->value }}
                                        </p>

                                </div>
                                <div class="w-[59%] max-md:w-full">
                                    <h3 class="py-2 mb-2 text-sm text-black">Scheduled Interview Date</h3>
                                        @if ($interview->interviewSchedule)
                                        <div class="px-4 py-3 border border-black rounded borderbg-gray-200 ">
                                            <p class="mb-2 text-xs">
                                                <span class="pr-2 font-bold">{{ $interview->interviewSchedule->getFormattedInterviewDate() }}</span> <span class="pl-2 font-bold">{{ $interview->interviewSchedule->getFormattedInterviewStartTime() }}</span>
                                            </p>
                                            <p class="text-sm">Interview number : {{ $interview->id }}</p>
                                        </div>
                                        @else
                                        <div class="px-4 py-5 border border-black rounded borderbg-gray-200 ">
                                            <p>No interview schedule found.</p>
                                        </div>
                                        @endif
                                </div>
                            </div>

                            <p class="mt-6 text-base">Name of person in charge :
                                {{ $interview->inchargeUser->name ?? 'N/A' }}
                            </p>
                            @if ($interview->interview_schedule_id)
                            <p class="mt-3 text-base">Job offer number : {{ $interview->interview_schedule_id }}</p>
                            @endif
                            @if ($interview->reason)
                                <p class="mt-3 text-base">Reason : {{ $interview->reason }}</p>
                            @endif

                            {{-- this section will not show if the user_type is Candidate --}}
                            @if (Auth::user()->user_type !== 'Candidate')
                                <h3 class="mt-8 text-base font-semibold">Memo</h3>
                                <form wire:submit.prevent="saveMemo" class="mt-2">
                                    <div class="relative">
                                        <textarea wire:model.defer="memoContent" class="w-full h-24 p-2 resize-y"></textarea>
                                        <div class="absolute bottom-2 right-2">
                                            <button type="submit"
                                                class="relative px-4 py-1 mb-2 bg-white border border-black rounded hover:bg-gray-100">Save</button>
                                        </div>
                                    </div>
                                    @error('memoContent')
                                        <span class="text-sm text-red-500">{{ $message }}</span>
                                    @enderror
                                </form>


                                <h3 class="mt-8 text-base font-semibold">Entry records</h3>
                                <ul class="mt-2 space-y-1 text-sm">
                                    @foreach ($interview->memos->sortByDesc('created_at') as $memo)
                                        <li><b>{{ $memo->user->name }}</b> : {{ $memo->content }}
                                            ({{ $memo->created_at->format('Y/m/d H:i') }})
                                        </li>
                                    @endforeach
                                    @if (!($interview->memos->count() > 0))
                                        <li>No entry records available</li>
                                    @endif
                                </ul>
                            @endif
                        </div>
                    @endif
                </div>
            </article>
            <nav
                class="flex gap-5 justify-between self-center mt-20 max-w-full text-sm text-center text-black w-[573px] max-md:flex-wrap max-md:mt-10">
                <a href="#" wire:click="returnToInterviewList"
                    class="justify-center px-8 py-6 bg-white border border-black border-solid rounded-md max-md:px-5">Return
                    to<br>List of Interview Status</a>
                <a href="#"
                    class="justify-center py-8 bg-white border border-black border-solid rounded-md px-11 max-md:px-5"
                    onclick="window.scrollTo({ top: 0, behavior: 'smooth' });">Return to Top page</a>
            </nav>
        </div>
    </section>
</div>
