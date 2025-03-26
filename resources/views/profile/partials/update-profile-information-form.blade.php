<section>
    <div class="card mb-4 border-0">
        <div class="card-body">
            <header class="mb-4">
                <h2 class="h5 card-title">
                    {{ __('Profile Information') }}
                </h2>
                <p class="card-text">
                    {{ __("Update your account's profile information and email address.") }}
                </p>
            </header>

            <!-- Form to send verification -->
            <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                @csrf
            </form>

            <!-- Profile Update Form -->
            <form method="post" action="{{ route('profile.update') }}" class="mt-4">
                @csrf
                @method('patch')

                <!-- Name Field -->
                <div class="mb-3">
                    <label for="name" class="form-label">
                        {{ __('Name') }}
                    </label>
                    <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                    @if ($errors->has('name'))
                        <div class="text-danger mt-2">
                            @foreach ($errors->get('name') as $message)
                                {{ $message }}
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Email Field -->
                <div class="mb-3">
                    <label for="email" class="form-label">
                        {{ __('Email') }}
                    </label>
                    <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="username" />
                    @if ($errors->has('email'))
                        <div class="text-danger mt-2">
                            @foreach ($errors->get('email') as $message)
                                {{ $message }}
                            @endforeach
                        </div>
                    @endif

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="mt-3">
                            <p class="text-sm text-gray-800">
                                {{ __('Your email address is unverified.') }}

                                <button form="send-verification" class="btn btn-link p-0 m-0 text-decoration-underline text-gray-600 hover:text-gray-900">
                                    {{ __('Click here to re-send the verification email.') }}
                                </button>
                            </p>

                            @if (session('status') === 'verification-link-sent')
                                <p class="mt-2 text-success mb-0">
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Save Button and Success Message -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Save') }}
                    </button>
                    
                    @if (session('status') === 'profile-updated')
                        <p class="text-success mb-0">
                            {{ __('Saved.') }}
                        </p>
                    @endif
                </div>
            </form>
        </div>
    </div>
</section>
