<div>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-center mb-4">Schedule Management</h1>
        <p class="text-center mb-8">Please select a date and time for your interview availability.</p>

        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">February 2024</h2>
                <a href="#" class="text-blue-600 hover:text-blue-800">Next Month ▶</a>
            </div>

            <div class="grid grid-cols-7 gap-2 mb-4">
                @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                    <div class="text-center font-medium p-2 bg-gray-100">{{ $day }}</div>
                @endforeach
            </div>

            <div class="grid grid-cols-7 gap-2">
                @for ($i = 1; $i <= 31; $i++)
                    <div class="border p-2 min-h-[80px] {{ in_array($i, [8,9,10,14,15,17,23]) ? 'bg-blue-100' : '' }}">
                        <span class="block text-sm mb-1">{{ $i }}</span>
                        @if (in_array($i, [8,9,10,14,15,17]))
                            <span class="block text-xs bg-blue-200 p-1 rounded">2:00 PM - 4:00 PM</span>
                        @elseif ($i == 23)
                            <span class="block text-xs bg-blue-200 p-1 rounded">12:00 PM - 2:00 PM</span>
                        @endif
                    </div>
                @endfor
            </div>
        </div>

        <div class="text-center">
            <a href="#" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-300">
                Back to Homepage
            </a>
        </div>

        <!-- Popups -->
        <div class="fixed inset-0 bg-black bg-opacity-50 hidden" x-data="{ open: false }" x-show="open" x-on:keydown.escape.window="open = false">
            <div class="flex items-center justify-center min-h-screen">
                <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                    <h3 class="text-lg font-semibold mb-4">Create New Schedule</h3>
                    <div class="flex justify-between mb-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Start Time</label>
                            <div class="flex items-center">
                                <select class="form-select mr-2">
                                    @for ($i = 0; $i <= 23; $i++)
                                        <option>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                    @endfor
                                </select>
                                :
                                <select class="form-select ml-2">
                                    <option>00</option>
                                    <option>30</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">End Time</label>
                            <div class="flex items-center">
                                <select class="form-select mr-2">
                                    @for ($i = 0; $i <= 23; $i++)
                                        <option>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                    @endfor
                                </select>
                                :
                                <select class="form-select ml-2">
                                    <option>00</option>
                                    <option>30</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button class="w-full bg-blue-600 text-white rounded-md py-2 hover:bg-blue-700 transition duration-300">
                        Create Schedule
                    </button>
                    <div class="mt-4">
                        <h4 class="font-medium mb-2">Created Schedules</h4>
                        <div class="flex justify-between items-center text-sm">
                            <span>12:00 - 14:00</span>
                            <div>
                                <button class="px-2 py-1 bg-gray-200 rounded mr-2 hover:bg-gray-300 transition duration-300">Edit</button>
                                <button class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition duration-300">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
