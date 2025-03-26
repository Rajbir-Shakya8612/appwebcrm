<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold fs-4 text-gray-800">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="row justify-content-center g-4">
                <!-- Update Profile Information -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                <!-- Update Password -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

                <!-- Delete User -->
                {{-- <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
</x-app-layout>
