<div x-data="{ isOpen: false }">
    <!-- Admin Navigation Menu -->
    <nav class="rounded-lg w-full shadow-md">
        <div class="w-full max-w-7xl mx-auto">
            <button @click="isOpen = !isOpen" type="button"
                class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
                aria-controls="navbar-multi-level" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 1h15M1 7h15M1 13h15" />
                </svg>
            </button>
            <div class="flex flex-col md:flex-row md:items-center bg-white p-2">
                <div class="w-full md:flex md:justify-center py-2" :class="{ 'hidden': !isOpen }"
                    id="navbar-multi-level">
                    <ul x-cloak
                        class="flex flex-col md:flex-row items-center justify-center space-y-4 md:space-y-0 md:space-x-0 font-medium p-4 md:p-0 mt-4 border rounded-lg bg-gray-50 md:mt-0 md:border-0 md:bg-white">

                        <li class="hidden md:block w-px h-6 bg-black mx-4"></li>

                        <li class="relative group px-4 py-2 md:py-0 md:border-r md:border-black"
                            x-data="{ open: false }">
                            <a href="#" @click="open = !open"
                                class="flex items-center justify-between text-sm text-gray-900 hover:text-blue-700">
                                Main menu
                                <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 4 4 4-4" />
                                </svg>
                            </a>
                            <ul x-show="open" @click.away="open = false"
                                class="z-10 font-normal bg-white divide-y divide-gray-100 rounded-lg shadow w-44 absolute left-0 mt-2">
                                <li><a href="/#notice-news" class="block px-4 py-2 hover:bg-gray-100">Notice/News</a>
                                </li>
                                <li><a href="{{ route('messages') }}"
                                        class="block px-4 py-2 hover:bg-gray-100">Message</a></li>
                            </ul>
                        </li>

                        <li class="relative group px-4 py-2 md:py-0 md:border-r md:border-black"
                            x-data="{ open: false }">
                            <a href="#" @click="open = !open"
                                class="flex items-center justify-between text-sm text-gray-900 hover:text-blue-700">
                                Job Seeker
                                <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 4 4 4-4" />
                                </svg>
                            </a>
                            <ul x-show="open" @click.away="open = false"
                                class="z-10 font-normal bg-white divide-y divide-gray-100 rounded-lg shadow w-44 absolute left-0 mt-2">
                                <li><a href="{{ route('job-seeker.search') }}"
                                        class="block px-4 py-2 hover:bg-gray-100">Job Seeker Search</a>
                                </li>
                            </ul>
                        </li>

                        <li class="relative group px-4 py-2 md:py-0 md:border-r md:border-black"
                            x-data="{ open: false }">
                            <a href="#" @click="open = !open"
                                class="flex items-center justify-between text-sm text-gray-900 hover:text-blue-700">
                                Register for Job Postings
                                <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 4 4 4-4" />
                                </svg>
                            </a>
                            <ul x-show="open" @click.away="open = false"
                                class="z-10 font-normal bg-white divide-y divide-gray-100 rounded-lg shadow w-44 absolute left-0 mt-2">
                                <li><a href="{{ route('job-list.search') }}"
                                        class="block px-4 py-2 hover:bg-gray-100">List of job registration
                                        information</a></li>
                            </ul>
                        </li>

                        <li class="relative group px-4 py-2 md:py-0 md:border-r md:border-black"
                            x-data="{ open: false }">
                            <a href="#" @click="open = !open"
                                class="flex items-center justify-between text-sm text-gray-900 hover:text-blue-700">
                                Interview
                                <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 4 4 4-4" />
                                </svg>
                            </a>
                            <ul x-show="open" @click.away="open = false"
                                class="z-10 font-normal bg-white divide-y divide-gray-100 rounded-lg shadow w-44 absolute left-0 mt-2">
                                <li><a href="{{ route('job-interviews') }}"
                                        class="block px-4 py-2 hover:bg-gray-100">List of Interview
                                        Status</a></li>
                                <li><a href="{{route('company.interview-schedule')}}" class="block px-4 py-2 hover:bg-gray-100">My Schedule</a></li>
                            </ul>
                        </li>

                        <li class="relative group px-4 py-2 md:py-0" x-data="{ open: false }">
                            <a href="#" @click="open = !open"
                                class="flex items-center justify-between text-sm text-gray-900 hover:text-blue-700">
                                Setting
                                <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 4 4 4-4" />
                                </svg>
                            </a>
                            <ul x-show="open" @click.away="open = false"
                                class="z-10 font-normal bg-white divide-y divide-gray-100 rounded-lg shadow w-44 absolute left-0 mt-2">
                                <li><a href="{{ route('company.edit-profile') }}"
                                        class="block px-4 py-2 hover:bg-gray-100">User Registration</a>
                                </li>
                                <li><a href="{{ route('company.edit-company-info') }}"
                                        class="block px-4 py-2 hover:bg-gray-100">Company Information
                                        Registration</a></li>
                                @auth
                                    @if (Auth::user()->user_type === 'CompanyAdmin')
                                        <li>
                                            <a href="{{ route('company.approve-representatives') }}"
                                                class="block px-4 py-2 hover:bg-gray-100">
                                                Pending Account
                                            </a>
                                        </li>
                                    @endif

                                    <!-- Other authenticated user menu items -->
                                @endauth
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-left block px-4 py-2 hover:bg-gray-100">Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>

                        <li class="hidden md:block w-px h-6 bg-black mx-4"></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <div class="flex justify-between items-center p-5 max-w-7xl mx-auto">
        <div></div>
        @include('home.partials.profile-nav')
    </div>
</div>
