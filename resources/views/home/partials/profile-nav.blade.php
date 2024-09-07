<div class="flex items-center justify-start gap-2">
    <div>
        <img src="{{ Auth::user()->image ? Storage::url(Auth::user()->image) : asset('placeholder.png') }}"
             alt="Profile Picture"
             class="bg-gray-100 rounded-full w-[40px] h-[40px]">
    </div>
    <div class="flex flex-col">
        @if (Auth::user()->user_type === 'Student')
            @if (Auth::user()->student)
                <span class="text-sm font-semibold">{{ Auth::user()->name }}</span>
                {{-- <span class="text-xs text-gray-600">{{ Auth::user()->student->name_katakana }}</span> --}}
            @endif
        @elseif (Auth::user()->user_type === 'CompanyRepresentative')
            @if (Auth::user()->companyRepresentative)
                <span class="text-sm font-semibold">{{ Auth::user()->name }}</span>
                <span class="text-xs text-gray-900">Representative</span>
                {{-- <span class="text-sm font-semibold">{{ Auth::user()->companyRepresentative->name_kanji }}</span> --}}
                {{-- <span class="text-xs text-gray-600">{{ Auth::user()->companyRepresentative->name_katakana }}</span> --}}
                @if (Auth::user()->companyRepresentative->company)
                    <span class="text-xs text-gray-600">{{ Auth::user()->companyRepresentative->company->name }}</span>
                @endif
            @endif
        @elseif (Auth::user()->user_type === 'CompanyAdmin')
            @if (Auth::user()->companyAdmin)
                <span class="text-sm font-semibold">{{ Auth::user()->name }}</span>
                <span class="text-xs text-gray-900">Admin</span>
                {{-- <span class="text-sm font-semibold">{{ Auth::user()->companyAdmin->name_kanji }}</span> --}}
                {{-- <span class="text-xs text-gray-600">{{ Auth::user()->companyAdmin->name_katakana }}</span> --}}
                @if (Auth::user()->companyAdmin->company)
                    <span class="text-xs text-gray-600">{{ Auth::user()->companyAdmin->company->name }}</span>
                @endif
            @endif
        @elseif (Auth::user()->user_type === 'BusinessOperator')
            @if (Auth::user()->businessOperator)
                <span class="text-sm font-semibold">{{ Auth::user()->name }}</span>
                <span class="text-xs text-gray-900">Business Operator</span>
                {{-- <span class="text-sm font-semibold">{{ Auth::user()->businessOperator->name_kanji }}</span> --}}
                {{-- <span class="text-xs text-gray-600">{{ Auth::user()->businessOperator->name_katakana }}</span> --}}
            @endif
        @elseif (Auth::user()->user_type === 'Candidate')
            @if (Auth::user()->student)
                <span class="text-sm font-semibold">{{ Auth::user()->name }}</span>
                {{-- <span class="text-xs text-gray-600">{{ Auth::user()->student->name_katakana }}</span> --}}
            @endif
        @endif
    </div>
</div>
