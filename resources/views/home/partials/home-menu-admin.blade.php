<div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
    <a
        {{--                            href="{{route('job-search')}}"--}}
        class="bg-white rounded-lg shadow-md border border-black border-solid p-6 hover:bg-gray-50 transition-colors duration-300 flex flex-col items-center gap-2"
    >
        <span class="text-xl font-bold">Job Seeker Search</span>
        {{--                <span class="text-gray-600">Find job opportunities</span>--}}
    </a>

    <a
{{--        href="{{route('interview-preparation-study-plan')}}"--}}
        class="bg-white rounded-lg shadow-md border border-black border-solid p-6 hover:bg-gray-50 transition-colors duration-300 flex flex-col items-center gap-2"
    >
        <span class="text-xl font-bold">Register for Job Postings</span>
        {{--                <span class="text-gray-600">Prepare for interviews</span>--}}
    </a>

    <a
        href="#"
        class="bg-white rounded-lg shadow-md border border-black border-solid p-6 hover:bg-gray-50 transition-colors duration-300 flex flex-col items-center gap-2"
    >
        <span class="text-xl font-bold">Status of Interview Examination</span>
        {{--                <span class="text-gray-600">Apply for Japanese lessons</span>--}}
    </a>

    <a href="{{ route('messages') }}"
        class="bg-white rounded-lg shadow-md border border-black border-solid p-6 hover:bg-gray-50 transition-colors duration-300 flex flex-col items-center gap-2">
        <span class="text-xl font-bold">Message</span>

    </a>
</div>
