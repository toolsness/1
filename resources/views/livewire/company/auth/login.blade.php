<div>
    <main class="flex flex-col items-center flex-grow max-w-md py-12 mx-auto md:max-w-lg">
        <h2 class="mb-8 text-2xl font-bold text-gray-800">Company Login</h2>
        <form wire:submit.prevent="login" class="w-full max-w-md">
            @if($errorMessage)
                <div class="p-4 mb-4 text-red-700 bg-red-100 rounded">
                    {{ $errorMessage }}
                </div>
            @endif
            @if($isBlocked)
                <div class="p-4 mb-4 text-yellow-700 bg-yellow-100 rounded">
                    Your account is temporarily blocked. Please try again in {{ $blockDuration }} seconds or reset your password.
                </div>
            @endif
            <div class="mb-6 grid grid-cols-[1fr_auto] gap-5 items-center">
                <label class="block mb-2 font-bold text-gray-700" for="email">
                    Email Address
                </label>
                <div class="flex flex-col">
                    <input
                        class="appearance-none border w-[300px] rounded flex-1 py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        wire:model.defer="email" type="email" placeholder="Enter your email address" />
                    @error('email')
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="mb-8 grid grid-cols-[1fr_auto] gap-5 items-center">
                <label class="block mb-2 font-bold text-gray-700" for="password">
                    Password
                </label>
                <div class="flex flex-col">
                    <input
                        class="appearance-none border rounded w-[300px] py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        wire:model.defer="password" type="password" placeholder="Enter your password" />
                    @error('password')
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="items-center mb-6 text-center">
                <p class="text-gray-700">If you are not yet a member, <a class="text-blue-500 hover:underline hover:uppercase" title="New Company Representative Registration" href="{{ route('company.new-member-registration') }}">click here.</a></p>
            </div>
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center pl-6">
                    <input type="checkbox" title="Remember login details, So that you don't have to login again with each visit!" wire:model.defer="remember" id="remember" class="mr-2">
                    <label for="remember" class="text-gray-700">Stay signed in</label>
                </div>
                <div>
                    <a href="{{ route('company.password.request') }}" title="If you have forgotten your password please click here to reset it!" class="text-blue-500 hover:underline hover:uppercase">Forgot Password?</a>
                </div>
            </div>
            <div class="flex justify-center space-x-4">
                <button
                    wire:loading.remove wire:target="login"
                    class="px-6 py-3 font-bold text-black bg-white border border-black rounded shadow-md focus:outline-none focus:shadow-outline"
                    type="submit">
                    Login
                </button>
                <div wire:loading wire:target="login"
                    class="inline-flex items-center justify-center">
                    <span class="font-bold text-blue-700"><i class="fa fa-spinner fa-spin"></i> <span>
                        Logging...</span></span>
                </div>
            </div>
            <div class="flex justify-center mt-12">
                <a href="{{ route('home') }}"
                    class="px-6 py-3 font-bold text-black transition duration-300 bg-white border border-black rounded shadow-md">
                    Back to Home
                </a>
            </div>
        </form>
    </main>
</div>
