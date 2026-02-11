<div id="auth-modal" style="display:none; position:fixed; inset:0; z-index:1000; justify-content:center; align-items:center; opacity:0; transition:opacity 0.3s ease;">
    <!-- Backdrop -->
    <div style="position:absolute; inset:0; background:rgba(0,0,0,0.6); backdrop-filter:blur(5px);" onclick="closeAuthModal()"></div>

    <!-- Modal Card -->
    <div style="position:relative; width:100%; max-width:400px; background:rgba(255, 255, 255, 0.05); backdrop-filter:blur(20px); border:1px solid rgba(255, 255, 255, 0.1); border-radius:12px; overflow:hidden; box-shadow:0 25px 50px -12px rgba(0, 0, 0, 0.5); transform:scale(0.95); transition:transform 0.3s ease;">
        
        <!-- Tabs -->
        <div id="auth-tabs" style="display:flex; border-bottom:1px solid rgba(255,255,255,0.1);">
            <button onclick="switchAuthTab('login')" id="tab-login" style="flex:1; padding:16px; background:transparent; border:none; color:#fff; font-weight:600; cursor:pointer; border-bottom:2px solid var(--accent); opacity:1;">
                {{ __('Login') }}
            </button>
            <button onclick="switchAuthTab('register')" id="tab-register" style="flex:1; padding:16px; background:transparent; border:none; color:rgba(255,255,255,0.6); font-weight:600; cursor:pointer; border-bottom:2px solid transparent;">
                {{ __('Register') }}
            </button>
            <div style="position:absolute; top:16px; right:16px; display:flex; gap:8px; z-index:10;">
                <a href="{{ route('lang.switch', 'en') }}" style="color:{{ app()->getLocale() == 'en' ? '#fff' : 'rgba(255,255,255,0.4)' }}; text-decoration:none; font-size:11px; font-weight:bold;">EN</a>
                <a href="{{ route('lang.switch', 'de') }}" style="color:{{ app()->getLocale() == 'de' ? '#fff' : 'rgba(255,255,255,0.4)' }}; text-decoration:none; font-size:11px; font-weight:bold;">DE</a>
            </div>
        </div>
        
        <!-- Forgot Password Header (Only visible in forgot mode) -->
        <div id="header-forgot" style="display:none; padding:16px; border-bottom:1px solid rgba(255,255,255,0.1); align-items:center;">
            <button onclick="switchAuthTab('login')" style="background:transparent; border:none; color:var(--muted); cursor:pointer; font-size:14px; display:flex; align-items:center; gap:6px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                {{ __('Back to Login') }}
            </button>
            <div style="margin-left:auto; font-weight:600; color:#fff;">{{ __('Reset Password') }}</div>
        </div>

        <!-- Login Form -->
        <div id="form-login" style="padding:24px;">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <!-- Email -->
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.7; margin-bottom:4px;">{{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus 
                           style="width:100%; padding:10px; border-radius:6px; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff; outline:none;">
                    @error('email') <div style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
                </div>

                <!-- Password -->
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.7; margin-bottom:4px;">{{ __('Password') }}</label>
                    <input type="password" name="password" required autocomplete="current-password"
                           style="width:100%; padding:10px; border-radius:6px; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff; outline:none;">
                    @error('password') <div style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
                </div>

                <!-- Remember Me -->
                <div style="margin-bottom:16px; display:flex; align-items:center;">
                   <label style="display:flex; align-items:center; cursor:pointer; font-size:14px; color:rgba(255,255,255,0.8);">
                       <input type="checkbox" name="remember" style="margin-right:8px; accent-color:var(--accent);">
                       {{ __('Remember me') }}
                   </label>
                </div>

                <button type="submit" style="width:100%; padding:12px; background:var(--accent); color:#fff; border:none; border-radius:6px; font-weight:600; cursor:pointer; transition:background 0.2s;">
                    {{ __('Log in') }}
                </button>

                <div style="margin-top:16px; text-align:center;">
                    @if (Route::has('password.request'))
                        <a href="#" onclick="event.preventDefault(); switchAuthTab('forgot')" style="color:var(--muted); font-size:12px; text-decoration:none;">{{ __('Forgot your password?') }}</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Register Form -->
        <div id="form-register" style="padding:24px; display:none;">
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <!-- Name -->
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.7; margin-bottom:4px;">{{ __('Name') }}</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                           style="width:100%; padding:10px; border-radius:6px; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff; outline:none;">
                    @error('name') <div style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
                </div>

                <!-- Email -->
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.7; margin-bottom:4px;">{{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           style="width:100%; padding:10px; border-radius:6px; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff; outline:none;">
                </div>

                <!-- Password -->
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.7; margin-bottom:4px;">
                        {{ __('Password') }}
                        <span style="font-size:10px; opacity:0.7; text-transform:none; margin-left:4px;">{{ __('(min. 8 chars, mixed case, numbers)') }}</span>
                    </label>
                    <input type="password" name="password" required autocomplete="new-password"
                           style="width:100%; padding:10px; border-radius:6px; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff; outline:none;">
                     @error('password', 'register') <div style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
                </div>

                <!-- Confirm Password -->
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.7; margin-bottom:4px;">{{ __('Confirm Password') }}</label>
                    <input type="password" name="password_confirmation" required
                           style="width:100%; padding:10px; border-radius:6px; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff; outline:none;">
                </div>

                <button type="submit" style="width:100%; padding:12px; background:var(--accent); color:#fff; border:none; border-radius:6px; font-weight:600; cursor:pointer; transition:background 0.2s;">
                    {{ __('Register') }}
                </button>
            </form>
        </div>
        
        <!-- Forgot Password Form -->
        <div id="form-forgot" style="padding:24px; display:none;">
            <div style="margin-bottom:20px; font-size:13px; color:var(--muted); line-height:1.5;">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </div>

            @if (session('status'))
                <div style="margin-bottom:16px; font-size:13px; color:#4ade80; background:rgba(74, 222, 128, 0.1); padding:10px; border-radius:6px; border:1px solid rgba(74, 222, 128, 0.2);">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <!-- Hidden input to identify this form submission -->
                <input type="hidden" name="auth_action" value="forgot_password">
                
                <!-- Email -->
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.7; margin-bottom:4px;">{{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus 
                           style="width:100%; padding:10px; border-radius:6px; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff; outline:none;">
                    @if($errors->has('email') && old('auth_action') == 'forgot_password')
                        <div style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $errors->first('email') }}</div>
                    @endif
                    <!-- Fallback for standard Laravel redirect if auth_action is lost (less reliable but safe) -->
                    @if($errors->has('email') && !old('auth_action') && !$errors->hasBag('register') && !old('password')) 
                         <!-- Heuristic: if we have email error but no password attempt, maybe it was this form? 
                              Actually, login requires password, so if password is empty it could be incomplete login OR forgot password.
                              Let's stick to auth_action hidden inputs primarily. -->
                    @endif
                </div>

                <button type="submit" style="width:100%; padding:12px; background:var(--accent); color:#fff; border:none; border-radius:6px; font-weight:600; cursor:pointer; transition:background 0.2s;">
                    {{ __('Email Password Reset Link') }}
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function openAuthModal(tab = 'login') {
        const modal = document.getElementById('auth-modal');
        const card = modal.querySelector('div[style*="position:relative"]');
        modal.style.display = 'flex';
        modal.style.pointerEvents = 'auto'; // Re-enable pointer events
        // Trigger reflow
        void modal.offsetWidth;
        modal.style.opacity = '1';
        card.style.transform = 'scale(1)';
        switchAuthTab(tab);
    }

    function closeAuthModal() {
        const modal = document.getElementById('auth-modal');
        const card = modal.querySelector('div[style*="position:relative"]');
        modal.style.pointerEvents = 'none'; // Disable pointer events immediately
        modal.style.opacity = '0';
        card.style.transform = 'scale(0.95)';
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }

    function switchAuthTab(tab) {
        // Handle Forgot Password specific layout
        if (tab === 'forgot') {
            document.getElementById('auth-tabs').style.display = 'none';
            document.getElementById('header-forgot').style.display = 'flex';
            document.getElementById('form-login').style.display = 'none';
            document.getElementById('form-register').style.display = 'none';
            document.getElementById('form-forgot').style.display = 'block';
            return;
        }

        // Reset to normal tabs layout for login/register
        document.getElementById('auth-tabs').style.display = 'flex';
        document.getElementById('header-forgot').style.display = 'none';
        document.getElementById('form-forgot').style.display = 'none';

        // Update tabs
        document.getElementById('tab-login').style.borderBottom = tab === 'login' ? '2px solid var(--accent)' : '2px solid transparent';
        document.getElementById('tab-login').style.opacity = tab === 'login' ? '1' : '0.6';
        
        document.getElementById('tab-register').style.borderBottom = tab === 'register' ? '2px solid var(--accent)' : '2px solid transparent';
        document.getElementById('tab-register').style.opacity = tab === 'register' ? '1' : '0.6';

        // Update forms
        document.getElementById('form-login').style.display = tab === 'login' ? 'block' : 'none';
        document.getElementById('form-register').style.display = tab === 'register' ? 'block' : 'none';
    }

    // Auto-open logic
    document.addEventListener('DOMContentLoaded', function() {
        @if (session('status'))
            // If we have a status session (likely "We have emailed your password reset link!"), open forgot tab
            openAuthModal('forgot');
        @elseif($errors->any())
            @if(old('auth_action') == 'forgot_password')
                openAuthModal('forgot');
            @elseif($errors->has('name') || $errors->hasBag('register'))
                openAuthModal('register');
            @else
                openAuthModal('login');
            @endif
        @endif
    });
</script>
