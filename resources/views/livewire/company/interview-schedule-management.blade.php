<div>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-center mb-4">Schedule Management</h1>
        <p class="text-center mb-8">Please select a date and time for your interview availability.</p>

        <div class="flex justify-center space-x-4 mb-4">
            <button wire:click="setViewMode('all')"
                class="px-4 py-2 bg-black text-white rounded-md hover:bg-green-700 transition duration-300 {{ $viewMode === 'all' ? 'bg-green-800 uppercase shadow-black shadow-md' : '' }}">
                All Slots
            </button>
            <button wire:click="setViewMode('personal')"
                class="px-4 py-2 bg-black text-white rounded-md hover:bg-green-700 transition duration-300 {{ $viewMode === 'personal' ? 'bg-green-800 uppercase shadow-black shadow-md' : '' }}">
                My Slots
            </button>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <button wire:click="previousMonth" class="text-blue-600 hover:text-blue-800">◀ Previous Month</button>
                <h2 class="text-xl font-semibold">
                    {{ Carbon\Carbon::create($currentYear, $currentMonth, 1)->format('F Y') }}</h2>
                <button wire:click="nextMonth" class="text-blue-600 hover:text-blue-800">Next Month ▶</button>
            </div>

            <div class="grid grid-cols-7 gap-2 mb-4">
                @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                    <div class="text-center font-medium p-2 bg-gray-100">{{ $day }}</div>
                @endforeach
            </div>

            <div class="grid grid-cols-7 gap-2">
                @foreach ($calendar as $week)
                    @foreach ($week as $day)
                        @if ($day)
                            @php
                                $dateString = sprintf('%s-%02d-%02d', $currentYear, $currentMonth, $day);
                            @endphp
                            <div wire:click="selectDate('{{ $dateString }}')"
                                class="border p-2 min-h-[80px] cursor-pointer hover:bg-blue-50 {{ isset($availableSlots[$dateString]) ? 'bg-blue-100' : '' }}">
                                <span class="block text-sm mb-1">{{ $day }}</span>
                                @if (isset($availableSlots[$dateString]))
                                    @foreach ($availableSlots[$dateString] as $slot)
                                        <span class="block text-xs bg-blue-200 p-1 rounded mb-1">
                                            {{ Carbon\Carbon::parse($slot['start_time'])->format('H:i') }} -
                                            {{ Carbon\Carbon::parse($slot['end_time'])->format('H:i') }}
                                        </span>
                                    @endforeach
                                @endif
                            </div>
                        @else
                            <div class="border p-2 min-h-[80px] bg-gray-100"></div>
                        @endif
                    @endforeach
                @endforeach
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('home') }}"
                class="inline-block px-6 py-3 bg-gray-600 text-white rounded-md hover:bg-blue-700 transition duration-300">
                Back to Homepage
            </a>
        </div>

        <!-- Modal -->
        @if ($showModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                    <h3 class="text-lg font-semibold mb-4">
                        {{ $isEditing ? 'Edit Schedule' : 'Create New Schedule' }} for {{ $selectedDate }}
                    </h3>
                    <form wire:submit.prevent="addOrUpdateTimeSlot">
                        <div class="flex justify-between mb-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Start Time</label>
                                <input type="time" wire:model.lazy="startTime" class="form-input">
                                @error('startTime')
                                    <span class="text-red-500 text-xs block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">End Time</label>
                                <input type="time" wire:model.lazy="endTime" class="form-input">
                                @error('endTime')
                                    <span class="text-red-500 text-xs block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- Inside the modal form -->
                        @if (Auth::user()->user_type === 'CompanyAdmin')
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-1">Assign User</label>
                                <select wire:model.lazy="selectedUserId" class="form-select w-full">
                                    <option value="{{ Auth::id() }}">Assign to myself</option>
                                    @foreach ($companyUsers as $user)
                                        @if ($user->id !== Auth::id())
                                            <option value="{{ $user->id }}">{{ $user->name }}
                                                ({{ $user->user_type }})</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <button type="submit"
                            class="w-full bg-blue-600 text-white rounded-md py-2 hover:bg-blue-700 transition duration-300">
                            {{ $isEditing ? 'Update Schedule' : 'Create Schedule' }}
                        </button>
                        @if ($isEditing)
                            <button type="button" wire:click="cancelEdit"
                                class="mt-2 w-full bg-gray-200 text-gray-800 rounded-md py-2 hover:bg-gray-300 transition duration-300">
                                Cancel Edit
                            </button>
                        @endif
                    </form>
                    <div class="mt-4">
                        <h4 class="font-medium mb-2">Created Schedules</h4>
                        @if (isset($availableSlots[$selectedDate]))
                            @foreach ($availableSlots[$selectedDate] as $slot)
                                <div
                                    class="flex justify-between items-center text-sm mb-2 border border-black space-y-1 shadow-md">
                                    <span class="p-2">
                                        <span
                                            class="font-bold">{{ Carbon\Carbon::parse($slot['start_time'])->format('H:i') }}
                                            -
                                            {{ Carbon\Carbon::parse($slot['end_time'])->format('H:i') }}</span>,
                                        <span class="font-semibold">{{ $slot['user']['name'] }}</span>, <span
                                            class="font-thin">
                                            @if ($slot['user']['user_type'] === 'CompanyAdmin')
                                                Company Admin
                                            @else
                                                Company Representative
                                            @endif
                                        </span>
                                    </span>
                                    <div>
                                        @if (Auth::user()->user_type === 'CompanyAdmin' || $slot['user_id'] === Auth::id())
                                            <button wire:click="editTimeSlot({{ $slot['id'] }})"
                                                class="px-2 py-1 bg-gray-200 rounded mr-2 hover:bg-gray-300 transition duration-300">Edit</button>
                                            <button wire:click="deleteTimeSlot({{ $slot['id'] }})"
                                                class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition duration-300">Delete</button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p>No schedules created for this date.</p>
                        @endif
                    </div>
                    <button wire:click="$set('showModal', false)"
                        class="mt-4 w-full bg-gray-200 text-gray-800 rounded-md py-2 hover:bg-gray-300 transition duration-300">
                        Close
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
