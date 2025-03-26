<section>
    <div class="card mb-4 border-0">
        <div class="card-body">
            <header class="mb-4">
                <h2 class="h5 card-title">
                    {{ __('Update Password') }}
                </h2>
                <p class="card-text">
                    {{ __('Ensure your account is using a long, random password to stay secure.') }}
                </p>
            </header>

            <form method="post" action="{{ route('password.update') }}" class="mt-4">
                @csrf
                @method('put')

                <!-- Current Password Field -->
                <div class="mb-3">
                    <label for="update_password_current_password" class="form-label">
                        {{ __('Current Password') }}
                    </label>
                    <input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password" />
                    @if ($errors->updatePassword->has('current_password'))
                        <div class="text-danger mt-2">
                            @foreach ($errors->updatePassword->get('current_password') as $message)
                                {{ $message }}
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- New Password Field -->
                <div class="mb-3">
                    <label for="update_password_password" class="form-label">
                        {{ __('New Password') }}
                    </label>
                    <input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password" />
                    @if ($errors->updatePassword->has('password'))
                        <div class="text-danger mt-2">
                            @foreach ($errors->updatePassword->get('password') as $message)
                                {{ $message }}
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Confirm Password Field -->
                <div class="mb-3">
                    <label for="update_password_password_confirmation" class="form-label">
                        {{ __('Confirm Password') }}
                    </label>
                    <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" />
                    @if ($errors->updatePassword->has('password_confirmation'))
                        <div class="text-danger mt-2">
                            @foreach ($errors->updatePassword->get('password_confirmation') as $message)
                                {{ $message }}
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Save Button and Success Message -->
                <div class="d-flex justify-content-between align-items-center">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Save') }}
                    </button>
                    @if (session('status') === 'password-updated')
                        <p class="text-success mb-0">
                            {{ __('Saved.') }}
                        </p>
                    @endif
                </div>
            </form>
        </div>
    </div>
</section>
