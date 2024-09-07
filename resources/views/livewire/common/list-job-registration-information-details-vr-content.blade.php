<div>
    <section class="flex justify-center items-center px-16 py-20 text-center text-black bg-white max-md:px-5">
        <div class="flex flex-col items-center mt-16 max-w-full w-[673px] max-md:mt-10">
            @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('message') }}</span>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="relative w-full">
                @if ($isEditing)
                    <label for="image-upload" class="cursor-pointer block relative">
                        @if ($newImage)
                            <img src="{{ $newImage->temporaryUrl() }}" alt="New VR content image" class="w-full aspect-[1.61] object-cover">
                        @else
                            <img src="{{ asset('storage/' . $vrContent->image) }}" alt="Current VR content image" class="w-full aspect-[1.61] object-cover">
                        @endif
                        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                            <span class="text-white text-lg">Click to upload new image</span>
                        </div>
                    </label>
                    <input id="image-upload" type="file" wire:model="newImage" class="hidden" accept="image/*">
                @else
                    <img loading="lazy" src="{{ asset('storage/' . $vrContent->image) }}" alt="Visual representation of {{ $contentType }} VR experience" class="w-full aspect-[1.61] object-cover">
                @endif
            </div>

            <h2 class="mt-4 text-xl font-semibold">{{ $contentType }}</h2>
            <p class="mt-2">{{ $vrContent->content_name }}</p>

            @if ($isEditing)
                <form wire:submit.prevent="save">
                    <input type="text" wire:model="newContentLink" class="mt-4 w-full px-4 py-2 border rounded" placeholder="Enter VR content link">
                    @error('newContentLink') <span class="text-red-500">{{ $message }}</span> @enderror
                    @error('newImage') <span class="text-red-500">{{ $message }}</span> @enderror

                    <button type="submit" class="justify-center items-center px-16 py-5 mt-16 max-w-full text-sm bg-green-500 text-white rounded-md border border-green-600 w-[200px] max-md:px-5 max-md:mt-10 hover:bg-green-600 transition">
                        Save
                    </button>
                </form>
            @else
                <button wire:click="playVR" class="justify-center items-center px-16 py-5 mt-16 max-w-full text-sm bg-white rounded-md border border-black border-solid w-[200px] max-md:px-5 max-md:mt-10 hover:bg-gray-100 transition">
                    Play VR Experience
                </button>
            @endif

            <button wire:click="toggleEdit" class="justify-center items-center px-16 py-5 mt-8 max-w-full text-sm bg-white rounded-md border border-black border-solid w-[200px] max-md:px-5 max-md:mt-10 hover:bg-gray-100 transition">
                {{ $isEditing ? 'Cancel' : 'Edit' }}
            </button>

            <a href="{{ route('job-details', $vacancy->id) }}" class="inline-block justify-center items-center px-16 py-5 mt-44 max-w-full text-xs bg-white rounded-md border border-black border-solid w-[242px] max-md:px-5 max-md:mt-10 hover:bg-gray-100 transition">
                Return to Job Details
            </a>
        </div>
    </section>
</div>