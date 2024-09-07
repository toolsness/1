<div>
    <div class="bg-white py-12 px-4 sm:px-6 lg:px-8">
        <form wire:submit.prevent="save">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-2xl font-bold mb-4 text-center">Create New Job Listing</h2>
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                    <div class="lg:col-span-4">
                        <div class="relative rounded-lg overflow-hidden">
                            <label for="image-upload" class="cursor-pointer">
                                @if ($image)
                                    <img src="{{ $image->temporaryUrl() }}" alt="New job offer background image"
                                        class="w-full h-96 object-cover">
                                @else
                                    <div class="w-full h-96 bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500">Click to upload an image</span>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                    <span class="text-white text-lg">Click to upload new image</span>
                                </div>
                            </label>
                            <input id="image-upload" type="file" wire:model="image" class="hidden" accept="image/*">
                            @error('image')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="lg:col-span-1 flex flex-col justify-end">
                        <div class="flex flex-col gap-4">
                            <a href="#"
                                class="w-full py-2 px-4 bg-white text-black text-sm border border-black rounded-md hover:bg-gray-100 transition">
                                Add Company Details VR
                            </a>
                            <a href="#"
                                class="w-full py-2 px-4 bg-white text-black text-sm border border-black rounded-md hover:bg-gray-100 transition">
                                Add Company Workspace VR
                            </a>
                            <p class="text-sm font-semibold">Company Name: {{ $companyName }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-12 space-y-6">
                    <div class="flex flex-col sm:flex-row gap-2 text-xl text-black">
                        <dt class="w-full sm:w-1/3 text-right font-semibold">Job Industry：</dt>
                        <dd class="w-full sm:w-2/3">
                            <select wire:model="vacancy_category_id" class="w-full border-gray-300 rounded-md">
                                <option value="">Select Job Industry</option>
                                @foreach ($vacancyCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('vacancy_category_id')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </dd>
                    </div>

                    {{-- <div class="flex flex-col sm:flex-row gap-2 text-xl text-black">
                        <dt class="w-full sm:w-1/3 text-right font-semibold">Job Title：</dt>
                        <dd class="w-full sm:w-2/3">
                            <input type="text" wire:model="job_title" class="w-full border-gray-300 rounded-md">
                            @error('job_title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </dd>
                    </div> --}}

                    @foreach ([
        'Job Title' => 'job_title',
        'Salary' => 'monthly_salary',
        'Shop Address' => 'work_location',
        'Office Hours' => 'working_hours',
        'Transportation Expenses' => 'transportation_expenses',
        'Overtime pay' => 'overtime_pay',
        'Bonus' => 'salary_increase_and_bonuses',
        'Social insurance' => 'social_insurance',
        'Language Requirement' => 'japanese_language',
        'Other' => 'other_details',
    ] as $label => $field)
                        <div class="flex flex-col sm:flex-row gap-2 text-xl text-black">
                            <dt class="w-full sm:w-1/3 text-right font-semibold">{{ $label }}：</dt>
                            <dd class="w-full sm:w-2/3">
                                @if ($field === 'other_details')
                                    <textarea wire:model="{{ $field }}" class="w-full border-gray-300 rounded-md"></textarea>
                                @else
                                    <input type="text" wire:model="{{ $field }}"
                                        class="w-full border-gray-300 rounded-md">
                                @endif
                                @error($field)
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </dd>
                        </div>
                    @endforeach
                    <div class="flex flex-col sm:flex-row gap-2 text-xl text-black">
                        <dt class="w-full sm:w-1/3 text-right font-semibold">Status：</dt>
                        <dd class="w-full sm:w-2/3">
                            <select wire:model="publish_category" class="w-full border-gray-300 rounded-md">
                                <option value="NotPublished">Not Published</option>
                                <option value="Published">Published</option>
                                <option value="PublicationStopped">Publication Stopped</option>
                            </select>
                            @error('publish_category')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </dd>
                    </div>
                </div>

                <div class="mt-8 flex justify-center">
                    <button type="submit"
                        class="px-6 py-2 bg-blue-500 text-white text-lg font-semibold rounded-md hover:bg-blue-600 transition">
                        Create Job Listing
                    </button>
                </div>
            </div>
        </form>

        <nav class="mt-12 flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('job-list.search') }}"
                class="px-6 py-3 bg-white text-black text-md text-center border border-black rounded-md hover:bg-gray-100 transition">
                Return to Job List
            </a>
        </nav>
    </div>
</div>
