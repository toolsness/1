<div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
    <a href="{{route('job-list.search')}}"
        class="bg-white rounded-lg shadow-md border border-black border-solid p-6 hover:bg-gray-50 transition-colors duration-300 flex flex-col items-center gap-2">
        <span class="text-xl font-bold">Job Search</span>
        {{--                <span class="text-gray-600">Find job opportunities</span> --}}
    </a>

    <a {{--        href="{{route('interview-preparation-study-plan')}}" --}}
        class="bg-white rounded-lg shadow-md border border-black border-solid p-6 hover:bg-gray-50 transition-colors duration-300 flex flex-col items-center gap-2">
        <span class="text-xl font-bold">Prepare for interviews</span>
        {{--                <span class="text-gray-600">Prepare for interviews</span> --}}
    </a>

    <a href="#"
        class="bg-white rounded-lg shadow-md border border-black border-solid p-6 hover:bg-gray-50 transition-colors duration-300 flex flex-col items-center gap-2">
        <span class="text-xl font-bold">Apply for Japanese lessons</span>
        {{--                <span class="text-gray-600">Apply for Japanese lessons</span> --}}
    </a>

    <a href="#"
        class="bg-white rounded-lg shadow-md border border-black border-solid p-6 hover:bg-gray-50 transition-colors duration-300 flex flex-col items-center gap-2">
        <span class="text-xl font-bold">Register as a user</span>
        {{--                <span class="text-gray-600">Register as a user</span> --}}
    </a>

    <a href="{{ route('messages') }}"
        class="bg-white rounded-lg shadow-md border border-black border-solid p-6 hover:bg-gray-50 transition-colors duration-300 flex flex-col items-center gap-2">
        <span class="text-xl font-bold">Message</span>

    </a>
</div>
