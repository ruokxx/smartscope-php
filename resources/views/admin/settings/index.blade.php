@extends('admin.layouts.app')

@section('admin-content')
<div class="card full">
    <h2>SMTP Configuration</h2>
    
    @if(session('success'))
        <div class="notice">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" class="upload-form">
        @csrf
        
        <div class="form-row">
            <label style="display:flex; align-items:center; cursor:pointer;">
                <input type="checkbox" name="smtp_enabled" value="1" {{ isset($settings['smtp_enabled']) && $settings['smtp_enabled'] ? 'checked' : '' }} style="width:auto; margin-right:8px;">
                Enable Registration Emails
            </label>
        </div>

        <div class="form-row">
            <label style="display:flex; align-items:center; cursor:pointer;">
                <input type="checkbox" name="enable_moderation" value="1" {{ isset($settings['enable_moderation']) && $settings['enable_moderation'] ? 'checked' : '' }} style="width:auto; margin-right:8px;">
                Enable Image Moderation (Require Approval)
            </label>
        </div>

        <div class="form-row">
            <label>Welcome Message (Verification Email)</label>
            <textarea name="welcome_message" rows="3" placeholder="Custom text to appear in the verification email...">{{ $settings['welcome_message'] ?? '' }}</textarea>
        </div>

        <div class="accent-line"></div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <h3>Server Settings</h3>
            <button type="button" onclick="loadAllInkl()" class="btn" style="padding:4px 8px; font-size:11px; background:rgba(255,255,255,0.05); color:var(--muted); font-weight:normal;">
                Load All-Inkl Preset
            </button>
        </div>

        <div class="row-2">
            <div class="form-row">
                <label>Mail Host</label>
                <input type="text" name="mail_host" value="{{ $settings['mail_host'] ?? '' }}" placeholder="smtp.example.com">
            </div>
            <div class="form-row">
                <label>Mail Port</label>
                <input type="number" name="mail_port" value="{{ $settings['mail_port'] ?? '587' }}">
            </div>
        </div>

        <div class="row-2">
            <div class="form-row">
                <label>Mail Username</label>
                <input type="text" name="mail_username" value="{{ $settings['mail_username'] ?? '' }}">
            </div>
            <div class="form-row">
                <label>Mail Password</label>
                <input type="password" name="mail_password" value="{{ $settings['mail_password'] ?? '' }}">
            </div>
        </div>

        <div class="form-row">
            <label>Encryption</label>
            <div class="styled-select-container">
                <select name="mail_encryption" class="styled-select">
                    <option value="tls" {{ ($settings['mail_encryption'] ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                    <option value="ssl" {{ ($settings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                    <option value="null" {{ ($settings['mail_encryption'] ?? '') == 'null' ? 'selected' : '' }}>None</option>
                </select>
            </div>
        </div>

        <div class="accent-line"></div>
        <h3>Sender Settings</h3>

        <div class="row-2">
            <div class="form-row">
                <label>From Address</label>
                <input type="email" name="mail_from_address" value="{{ $settings['mail_from_address'] ?? '' }}" placeholder="noreply@example.com">
            </div>
            <div class="form-row">
                <label>From Name</label>
                <input type="text" name="mail_from_name" value="{{ $settings['mail_from_name'] ?? config('app.name') }}">
            </div>
        </div>

        <button type="submit" class="btn" style="margin-top:12px;">Save Settings</button>
    </form>
    <div class="accent-line" style="margin-top:24px;"></div>
    <h3>Test Configuration</h3>
    
    @if(session('mail_error'))
        <div class="notice" style="background:rgba(239,68,68,0.1); border-color:rgba(239,68,68,0.2); color:#fca5a5;">
            <strong>Connection Failed:</strong><br>
            {{ session('mail_error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.test') }}" style="margin-bottom:24px;">
        @csrf
        <div style="display:flex; align-items:center; gap:12px;">
            <button type="submit" class="btn" style="background:#2c3e50; color:#fff;">
                Test Email to {{ auth()->user()->email }}
            </button>
            <span style="font-size:12px; color:var(--muted);">Save settings before testing!</span>
        </div>
    </form>
</div>

<script>
    function loadAllInkl() {
        if(!confirm('Overwrite current settings with All-Inkl defaults?')) return;
        
        document.querySelector('input[name="mail_host"]').value = 'kasserver.com';
        document.querySelector('input[name="mail_port"]').value = '465';
        document.querySelector('select[name="mail_encryption"]').value = 'ssl';
    }
</script>
@endsection
