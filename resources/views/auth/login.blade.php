<x-guest-layout>
    <div class="fixed inset-0 bg-gradient-to-b from-green-600 to-green-950 -z-10"></div>

    <div class="flex items-center justify-center min-h-screen px-3 sm:px-4 lg:px-6 w-full">
        <div
            class="w-full max-w-xs sm:max-w-sm md:max-w-md lg:max-w-sm xl:max-w-sm bg-white shadow-lg rounded-lg px-3 sm:px-4 md:px-5 py-4 sm:py-5 md:py-5">
            <x-auth-session-status class="mb-3" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="text-center mb-4">
                    <img src="{{ asset('asset/images/logo.png') }}" alt="SI-PATEN Logo"
                        class="mx-auto w-28 h-24 sm:w-32 sm:h-28 md:w-32 md:h-28 lg:w-40 lg:h-32 object-contain -mb-2">
                    <p class="text-xs sm:text-sm text-gray-500">
                        Selamat datang! Silakan masuk ke akun Anda.
                    </p>
                </div>

                <div>
                    <x-input-label for="email" value="Username" class="text-xs font-medium text-gray-600" />
                    <x-text-input id="email"
                        class="block mt-1 w-full px-2.5 py-1.5 sm:px-3 sm:py-2 border-gray-300 text-gray-800 focus:border-green-500 focus:ring-green-500 rounded-md placeholder:text-gray-400 text-xs sm:text-sm"
                        type="text" name="email" :value="old('email')" required autofocus autocomplete="username"
                        placeholder="Masukkan Username Anda" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
                </div>

                <div class="relative mt-3">
                    <x-input-label for="password" :value="__('Password')"
                        class="text-xs mb-1 font-medium text-gray-600" />

                    <div class="relative">
                        <x-text-input id="password"
                            class="block w-full px-2.5 py-1.5 sm:px-3 sm:py-2 border-gray-300 text-gray-800 focus:border-green-500 focus:ring-green-500 rounded-md placeholder:text-gray-400 text-xs sm:text-sm"
                            type="password" name="password" required autocomplete="current-password"
                            placeholder="Masukkan Password" style="padding-right:2rem" />

                        <!-- icon mata -->
                        <span class="absolute inset-y-0 right-3 flex items-center cursor-pointer"
                            onclick="togglePasswordVisibility()">

                            <!-- Mata terbuka -->
                            <svg id="eye-open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="currentColor" class="w-4 h-4 text-gray-600">
                                <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                <path fill-rule="evenodd"
                                    d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z"
                                    clip-rule="evenodd" />
                            </svg>

                            <!-- Mata tertutup -->
                            <svg id="eye-closed" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="currentColor" class="hidden w-4 h-4 text-gray-600">
                                <path
                                    d="M3.53 2.47a.75.75 0 0 0-1.06 1.06l18 18a.75.75 0 1 0 1.06-1.06l-18-18ZM22.676 12.553a11.249 11.249 0 0 1-2.631 4.31l-3.099-3.099a5.25 5.25 0 0 0-6.71-6.71L7.759 4.577a11.217 11.217 0 0 1 4.242-.827c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113Z" />
                                <path
                                    d="M15.75 12c0 .18-.013.357-.037.53l-4.244-4.243A3.75 3.75 0 0 1 15.75 12ZM12.53 15.713l-4.243-4.244a3.75 3.75 0 0 0 4.244 4.243Z" />
                                <path
                                    d="M6.75 12c0-.619.107-1.213.304-1.764l-3.1-3.1a11.25 11.25 0 0 0-2.63 4.31c-.12.362-.12.752 0 1.114 1.489 4.467 5.704 7.69 10.675 7.69 1.5 0 2.933-.294 4.242-.827l-2.477-2.477A5.25 5.25 0 0 1 6.75 12Z" />
                            </svg>

                        </span>
                    </div>

                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
                </div>

                <div class="block mt-3">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox"
                            class="rounded border-gray-300 text-green-600 focus:ring-green-500 " name="remember">
                        <span class="ms-1.5 text-xs text-gray-700">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-5">
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center px-3 py-2 bg-green-700 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 text-xs sm:text-sm">
                        {{ __('Login') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeOpen = document.getElementById('eye-open');
        const eyeClosed = document.getElementById('eye-closed');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        } else {
            passwordInput.type = 'password';
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
        }
    }
    </script>
    @endpush
</x-guest-layout>