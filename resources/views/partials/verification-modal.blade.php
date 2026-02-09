<div id="verification-modal" style="display:none; position:fixed; inset:0; z-index:1000; justify-content:center; align-items:center;">
    <!-- Backdrop -->
    <div style="position:absolute; inset:0; background:rgba(0,0,0,0.8); backdrop-filter:blur(5px);"></div>

    <!-- Modal Card -->
    <div style="position:relative; width:100%; max-width:400px; background:rgba(255, 255, 255, 0.05); backdrop-filter:blur(20px); border:1px solid rgba(255, 255, 255, 0.1); border-radius:12px; padding:24px; box-shadow:0 25px 50px -12px rgba(0, 0, 0, 0.5); text-align:center;">
        
        <h2 style="font-size:20px; margin-bottom:12px; color:#fff;">{{ __('Verify Your Email') }}</h2>
        
        <p style="color:#d1d5db; font-size:14px; line-height:1.5; margin-bottom:24px;">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?') }}
        </p>

        @if (session('status') == 'verification-link-sent')
            <div style="background:rgba(74, 222, 128, 0.1); border:1px solid rgba(74, 222, 128, 0.2); color:#4ade80; padding:12px; border-radius:8px; font-size:13px; margin-bottom:24px;">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <div style="display:flex; flex-direction:column; gap:12px;">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn" style="width:100%;">
                    {{ __('Resend Verification Email') }}
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="background:transparent; border:none; color:var(--muted); font-size:13px; cursor:pointer; text-decoration:underline;">
                    {{ __('Log Out') }}
                </button>
            </form>
            
            <button onclick="closeVerificationModal()" style="background:transparent; border:none; color:var(--muted); font-size:13px; cursor:pointer;">
                {{ __('Close') }}
            </button>
        </div>
    </div>
</div>

<script>
    function openVerificationModal() {
        document.getElementById('verification-modal').style.display = 'flex';
    }

    function closeVerificationModal() {
        document.getElementById('verification-modal').style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if (session('open_verification_modal'))
            openVerificationModal();
        @endif
    });
</script>
