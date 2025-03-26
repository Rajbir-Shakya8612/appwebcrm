<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <div class="input-group">
                <x-text-input id="password" class="block w-full" type="password" name="password" required
                    autocomplete="new-password" />
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <div class="input-group">
                <x-text-input id="password_confirmation" class="block w-full" type="password"
                    name="password_confirmation" required autocomplete="new-password" />
                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">


            {{-- <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button> --}}
            <button type="submit" class="btn btn-primary w-100 mt-3" id="registerButton">
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                Register
            </button>
        </div>
    </form>
    <div class="text-center mt-4">
        <p class="mb-0">Already have an account? <a href="{{ route('login') }}">Login</a></p>
    </div>
</x-guest-layout>
