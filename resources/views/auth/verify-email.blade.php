<x-guest-layout>
    <div style="color:#d1d5db; font-size: 0.875rem; line-height: 1.25rem; margin-bottom: 1rem;">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div style="color: #4ade80; font-weight: 500; font-size: 0.875rem; margin-bottom: 1rem;">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div style="margin-top: 1rem; display: flex; align-items: center; justify-content: space-between;">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <button type="submit" style="background: linear-gradient(90deg,var(--accent),var(--accent2)); color: #041229; font-weight: 700; padding: 0.5rem 1rem; border-radius: 0.375rem; border: none; cursor: pointer; transition: opacity 0.2s;">
                    {{ __('Resend Verification Email') }}
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" style="text-decoration: underline; font-size: 0.875rem; color: #9ca3af; background: transparent; border: none; cursor: pointer;">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
