@extends('admin.layouts.app')

@section('admin-content')
<div class="card full">
    <h2>SMTP Configuration</h2>
    
    @if(session('success'))
        <div class="notice">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" class="upload-form">
        @csrf

        <!-- General Settings -->
        <h3>General Settings</h3>
        <div class="card" style="background:rgba(255,255,255,0.02); padding:16px; margin-bottom:24px; border:1px solid rgba(255,255,255,0.05);">
            <div class="form-row">
                <label>Header Description</label>
                <input type="text" name="header_description" value="{{ $settings['header_description'] ?? '' }}" placeholder="Your Community for Smart Telescope Astrophotography">
                <div style="font-size:11px; color:var(--muted); margin-top:2px;">
                    Appears between the site title and navigation. Leave empty to use the default translation.
                </div>
            </div>
        </div>

        <!-- Domain & SSL Settings -->
        <h3>Domain & SSL</h3>
        <div class="card" style="background:rgba(255,255,255,0.02); padding:16px; margin-bottom:24px; border:1px solid rgba(255,255,255,0.05);">
            <div class="form-row">
                <label style="display:flex; align-items:center; cursor:pointer; color:var(--accent);">
                    <input type="checkbox" name="ssl_enabled" value="1" {{ isset($settings['ssl_enabled']) && $settings['ssl_enabled'] ? 'checked' : '' }} style="width:auto; margin-right:8px;">
                    <strong>Force HTTPS (SSL)</strong>
                </label>
                <div style="font-size:12px; color:var(--muted); margin-top:4px;">
                    Enabling this will force all application links to use <code>https://</code>. 
                    <strong>Important:</strong> You must ensure your web server is configured to serve HTTPS on port 443 with valid certificates. This setting does not configure the web server itself.
                </div>
            </div>

            <div class="form-row">
                <label>System Domain</label>
                <input type="text" name="system_domain" value="{{ $settings['system_domain'] ?? request()->getHost() }}" placeholder="example.com">
                <div style="font-size:11px; color:var(--muted); margin-top:2px;">The primary domain used for generating links.</div>
            </div>

            <div class="row-2">
                <div class="form-row">
                    <label>SSL Certificate (Public)</label>
                    <textarea name="ssl_certificate" rows="4" placeholder="-----BEGIN CERTIFICATE----- ... (Optional storage for reference)" style="font-family:monospace; font-size:11px;">{{ $settings['ssl_certificate'] ?? '' }}</textarea>
                </div>
                <div class="form-row">
                    <label>SSL Private Key</label>
                    <textarea name="ssl_private_key" rows="4" placeholder="-----BEGIN PRIVATE KEY----- ... (Optional storage for reference)" style="font-family:monospace; font-size:11px;">{{ $settings['ssl_private_key'] ?? '' }}</textarea>
                </div>
            </div>

            <div style="margin-top:16px; border-top:1px solid rgba(255,255,255,0.05); padding-top:16px;">
                <details style="background:rgba(255,255,255,0.01); border-radius:4px; overflow:hidden;">
                    <summary style="padding:12px; cursor:pointer; color:var(--accent); font-weight:600; user-select:none;">How to setup SSL / HTTPS?</summary>
                    <div style="padding:16px; font-size:13px; line-height:1.6; color:var(--muted);">
                        <strong style="color:#fff;">Linux (Nginx / Apache)</strong>
                        <ul style="margin:4px 0 16px 20px;">
                            <li><strong>Certbot (Recommended):</strong> The easiest way is using Let's Encrypt.
                                <br><code>sudo apt install certbot python3-certbot-nginx</code>
                                <br><code>sudo certbot --nginx -d example.com</code>
                            </li>
                            <li><strong>Manual:</strong> Place your `.crt` and `.key` files in `/etc/ssl/` and update your Nginx/Apache config to point to them (listen 443 ssl).</li>
                        </ul>

                        <strong style="color:#fff;">Windows</strong>
                        <ul style="margin:4px 0 0 20px;">
                            <li><strong>IIS:</strong> Use "Win-ACME", a simple tool to automatically generate and obtain free certificates for IIS.</li>
                            <li><strong>XAMPP / Apache:</strong> Enable the `ssl_module` in `httpd.conf`. Configure `httpd-ssl.conf` to point to your certificate files.</li>
                            <li><strong>Local Development:</strong> Use a tool like <code>mkcert</code> to generate locally trusted certificates.</li>
                        </ul>
                    </div>
                </details>
            </div>
        </div>

        <div class="accent-line"></div>

        <h3>SMTP Configuration</h3>
        
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

        <div class="accent-line"></div>

        <h3>Feature Toggles</h3>
        <div class="form-row">
            <label style="display:flex; align-items:center; cursor:pointer;">
                <input type="checkbox" name="community_enabled" value="1" {{ isset($settings['community_enabled']) && $settings['community_enabled'] ? 'checked' : '' }} style="width:auto; margin-right:8px;">
                Enable Community (Feed, Groups)
            </label>
        </div>
        <div class="form-row">
            <label style="display:flex; align-items:center; cursor:pointer;">
                <input type="checkbox" name="forum_enabled" value="1" {{ isset($settings['forum_enabled']) && $settings['forum_enabled'] ? 'checked' : '' }} style="width:auto; margin-right:8px;">
                Enable Forum
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
    </form>
    
    <div class="accent-line" style="margin-top:24px;"></div>
    
    <div class="card" style="padding:16px; margin-bottom:24px; background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.05);">
        <h3 style="margin-top:0; margin-bottom:16px; font-size:16px;">Database Maintenance</h3>
        <p style="color:var(--muted); margin-bottom:16px; font-size:13px;">Sync missing database records (e.g. new Deep Sky Objects) without losing existing data.</p>
        
        <form action="{{ route('admin.settings.sync_objects') }}" method="POST">
            @csrf
            <button type="submit" class="btn" style="background:var(--accent); color:#fff; border:none; padding:8px 16px; border-radius:4px; cursor:pointer;" onclick="return confirm('This will check for missing Deep Sky Objects and add them. existing objects will stay. Continue?')">
                🔄 Sync Deep Sky Objects
            </button>
        </form>
    </div>
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
