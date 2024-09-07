<div>
    <style>
        [x-cloak] {
            display: none !important;
        }

        .progress-ring {
            transform: rotate(-90deg);
        }
    </style>
    <div class="font-sans">
        <div class="container p-6 mx-auto">
            <div class="p-6 transition-colors duration-100 bg-white border border-black rounded-lg shadow-xl">
                @auth('web')
                    @if (Auth::user()->user_type === 'Student')
                        <div class="grid grid-cols-3">
                            <div class="px-4"></div>
                            <button
                                class="px-6 py-2 font-bold text-black transition duration-300 bg-white border border-black rounded-md shadow-lg md:grid-cols-4 hover:text-white hover:bg-green-700"
                                wire:click="startJobHunting" wire:loading.attr="disabled" wire:loading.remove>Start Job
                                Hunting</button>
                            <span wire:loading wire:target="startJobHunting">
                                <span class="font-bold text-green-700 "><i class="fa fa-spinner fa-spin"></i> <span class='font-extrabold'>
                                        Processing Start...</span>
                                </span>
                            </span>
                            <div class="px-4"></div>
                        </div>
                    @elseif (Auth::user()->user_type === 'Candidate')
                        <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-4">
                            @foreach ($tasks as $task)
                                <div class="text-center" x-data="{ percentage: {{ $task['percentage'] }} }">
                                    <svg class="w-24 h-24 mx-auto mb-2 progress-ring" viewBox="0 0 36 36">
                                        <circle cx="18" cy="18" r="16" fill="none"
                                            class="text-gray-300 stroke-current" stroke-width="3.6"></circle>
                                        <circle cx="18" cy="18" r="16" fill="none"
                                            class="stroke-current text-lime-400" stroke-width="3.6"
                                            :stroke-dasharray="2 * Math.PI * 16"
                                            :stroke-dashoffset="2 * Math.PI * 16 * (1 - percentage / 100)" x-cloak></circle>
                                    </svg>
                                    <p class="font-semibold">{{ $task['name'] }}</p>
                                    <p x-text="`${percentage}% Complete`"></p>
                                </div>
                            @endforeach
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div class="p-4 border border-black rounded shadow-lg">
                                <h2 class="mb-2 font-bold">Previous Task</h2>
                                <p>2023/11/1</p>
                                <p>Create self-introduction for interview</p>
                            </div>
                            <div class="p-4 border border-black rounded shadow-lg">
                                <h2 class="mb-2 font-bold">Next Task</h2>
                                <p>Regular Interview 2</p>
                            </div>
                            <div class="p-4 border border-black rounded shadow-lg">
                                <h2 class="mb-2 font-bold">Total Study Time</h2>
                                <p>13 hours</p>
                            </div>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>

</div>
