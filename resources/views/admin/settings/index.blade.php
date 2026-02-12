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

        <!-- Storage Configuration -->
        <h3>Storage Configuration</h3>
        <div class="card" style="background:rgba(255,255,255,0.02); padding:16px; margin-bottom:24px; border:1px solid rgba(255,255,255,0.05);">
            <div class="form-row">
                <label>Storage Driver</label>
                <div class="styled-select-container">
                    <select name="storage_driver" id="storageDriverSelect" class="styled-select" onchange="toggleStorageFields()">
                        <option value="public" {{ ($settings['storage_driver'] ?? 'public') == 'public' ? 'selected' : '' }}>Local (Public)</option>
                        <option value="s3" {{ ($settings['storage_driver'] ?? '') == 's3' ? 'selected' : '' }}>S3 Compatible (AWS, MinIO, DO Spaces)</option>
                        <option value="ftp" {{ ($settings['storage_driver'] ?? '') == 'ftp' ? 'selected' : '' }}>FTP</option>
                    </select>
                </div>
                <div style="font-size:11px; color:var(--muted); margin-top:2px;">
                    Changing this only affects <strong>new</strong> uploads. Existing images remain on their original disk.
                </div>
            </div>

            <!-- S3 Settings -->
            <div id="s3Settings" style="display:none; margin-top:16px; border-top:1px solid rgba(255,255,255,0.05); padding-top:16px;">
                <h4 style="margin-top:0;">S3 Configuration</h4>
                <div class="row-2">
                    <div class="form-row">
                        <label>Access Key ID</label>
                        <input type="text" name="s3_key" value="{{ $settings['s3_key'] ?? '' }}">
                    </div>
                    <div class="form-row">
                        <label>Secret Access Key</label>
                        <input type="password" name="s3_secret" value="{{ $settings['s3_secret'] ?? '' }}">
                    </div>
                </div>
                <div class="row-2">
                    <div class="form-row">
                        <label>Region</label>
                        <input type="text" name="s3_region" value="{{ $settings['s3_region'] ?? 'us-east-1' }}">
                    </div>
                    <div class="form-row">
                        <label>Bucket</label>
                        <input type="text" name="s3_bucket" value="{{ $settings['s3_bucket'] ?? '' }}">
                    </div>
                </div>
                <div class="form-row">
                    <label>Endpoint URL (Optional)</label>
                    <input type="text" name="s3_url" value="{{ $settings['s3_url'] ?? '' }}" placeholder="https://s3.example.com">
                </div>
                <div class="form-row">
                    <label style="display:flex; align-items:center; cursor:pointer;">
                        <input type="checkbox" name="s3_use_path_style_endpoint" value="1" {{ isset($settings['s3_use_path_style_endpoint']) && $settings['s3_use_path_style_endpoint'] ? 'checked' : '' }} style="width:auto; margin-right:8px;">
                        Use Path Style Endpoint (Required for MinIO)
                    </label>
                </div>
            </div>

            <!-- FTP Settings -->
            <div id="ftpSettings" style="display:none; margin-top:16px; border-top:1px solid rgba(255,255,255,0.05); padding-top:16px;">
                 <h4 style="margin-top:0;">FTP Configuration</h4>
                 <div class="row-2">
                    <div class="form-row">
                        <label>Host</label>
                        <input type="text" name="ftp_host" value="{{ $settings['ftp_host'] ?? '' }}">
                    </div>
                    <div class="form-row">
                        <label>Username</label>
                        <input type="text" name="ftp_username" value="{{ $settings['ftp_username'] ?? '' }}">
                    </div>
                 </div>
                 <div class="row-2">
                    <div class="form-row">
                        <label>Password</label>
                        <input type="password" name="ftp_password" value="{{ $settings['ftp_password'] ?? '' }}">
                    </div>
                    <div class="form-row">
                        <label>Root Folder</label>
                        <input type="text" name="ftp_root" value="{{ $settings['ftp_root'] ?? '/' }}">
                    </div>
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

        <h3>Email Templates</h3>
        <p style="color:var(--muted); font-size:13px; margin-bottom:16px;">
            Customize the system emails. Use placeholders <code>{username}</code> and <code>{action_url}</code>.
        </p>

        <!-- Tabs Style -->
        <style>
            .lang-tabs { display:flex; gap:8px; margin-bottom:12px; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px; }
            .lang-tab { background:transparent; border:none; color:var(--muted); cursor:pointer; padding:4px 8px; font-weight:600; font-size:13px; }
            .lang-tab.active { color:var(--accent); border-bottom:2px solid var(--accent); }
            .lang-tab:hover { color:#fff; }
        </style>

        <!-- Password Reset -->
        <div class="card" style="background:rgba(255,255,255,0.02); padding:16px; margin-bottom:16px; border:1px solid rgba(255,255,255,0.05);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <h4 style="margin:0;">Password Reset</h4>
                <div class="lang-tabs" style="margin:0; border:none; padding:0;">
                    <button type="button" class="lang-tab active" onclick="switchLang('reset', 'en')" id="tab-reset-en">EN</button>
                    <button type="button" class="lang-tab" onclick="switchLang('reset', 'de')" id="tab-reset-de">DE</button>
                </div>
            </div>
            
            <!-- EN -->
            <div id="content-reset-en">
                <div style="display:flex; justify-content:flex-end; margin-bottom:8px;">
                     <button type="button" onclick="loadDefaultReset('en')" class="btn" style="padding:2px 8px; font-size:10px; background:rgba(255,255,255,0.05); color:var(--muted);">Load Default (EN)</button>
                </div>
                <div class="form-row">
                    <label>Subject (EN)</label>
                    <input type="text" name="email_reset_subject_en" value="{{ $settings['email_reset_subject_en'] ?? 'Reset Password Notification' }}">
                </div>
                <div class="form-row">
                    <label>Body (EN)</label>
                    <textarea name="email_reset_body_en" rows="6">{{ $settings['email_reset_body_en'] ?? "You are receiving this email because we received a password reset request for your account.\n\n{action_url}\n\nThis password reset link will expire in :count minutes.\n\nIf you did not request a password reset, no further action is required." }}</textarea>
                </div>
            </div>

            <!-- DE -->
            <div id="content-reset-de" style="display:none;">
                <div style="display:flex; justify-content:flex-end; margin-bottom:8px;">
                     <button type="button" onclick="loadDefaultReset('de')" class="btn" style="padding:2px 8px; font-size:10px; background:rgba(255,255,255,0.05); color:var(--muted);">Load Default (DE)</button>
                </div>
                <div class="form-row">
                    <label>Subject (DE)</label>
                    <input type="text" name="email_reset_subject_de" value="{{ $settings['email_reset_subject_de'] ?? 'Passwort zurücksetzen' }}">
                </div>
                <div class="form-row">
                    <label>Body (DE)</label>
                    <textarea name="email_reset_body_de" rows="6">{{ $settings['email_reset_body_de'] ?? "Sie erhalten diese E-Mail, weil wir eine Anfrage zum Zurücksetzen des Passworts für Ihr Konto erhalten haben.\n\n{action_url}\n\nDieser Link zum Zurücksetzen des Passworts läuft in :count Minuten ab.\n\nWenn Sie kein Zurücksetzen des Passworts angefordert haben, ist keine weitere Aktion erforderlich." }}</textarea>
                </div>
            </div>
            <div style="font-size:11px; color:var(--muted); margin-top:4px;">Available placeholders: {username}, {action_url}, :count</div>
        </div>

        <!-- Verify Email -->
        <div class="card" style="background:rgba(255,255,255,0.02); padding:16px; margin-bottom:16px; border:1px solid rgba(255,255,255,0.05);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <h4 style="margin:0;">Verify Email</h4>
                <div class="lang-tabs" style="margin:0; border:none; padding:0;">
                    <button type="button" class="lang-tab active" onclick="switchLang('verify', 'en')" id="tab-verify-en">EN</button>
                    <button type="button" class="lang-tab" onclick="switchLang('verify', 'de')" id="tab-verify-de">DE</button>
                </div>
            </div>

            <!-- EN -->
            <div id="content-verify-en">
                <div style="display:flex; justify-content:flex-end; margin-bottom:8px;">
                     <button type="button" onclick="loadDefaultVerify('en')" class="btn" style="padding:2px 8px; font-size:10px; background:rgba(255,255,255,0.05); color:var(--muted);">Load Default (EN)</button>
                </div>
                <div class="form-row">
                    <label>Subject (EN)</label>
                    <input type="text" name="email_verify_subject_en" value="{{ $settings['email_verify_subject_en'] ?? 'Verify Email Address' }}">
                </div>
                <div class="form-row">
                    <label>Body (EN)</label>
                    <textarea name="email_verify_body_en" rows="6">{{ $settings['email_verify_body_en'] ?? "Please click the button below to verify your email address.\n\n{action_url}\n\nIf you did not create an account, no further action is required." }}</textarea>
                </div>
            </div>

            <!-- DE -->
            <div id="content-verify-de" style="display:none;">
                <div style="display:flex; justify-content:flex-end; margin-bottom:8px;">
                     <button type="button" onclick="loadDefaultVerify('de')" class="btn" style="padding:2px 8px; font-size:10px; background:rgba(255,255,255,0.05); color:var(--muted);">Load Default (DE)</button>
                </div>
                <div class="form-row">
                    <label>Subject (DE)</label>
                    <input type="text" name="email_verify_subject_de" value="{{ $settings['email_verify_subject_de'] ?? 'E-Mail-Adresse bestätigen' }}">
                </div>
                <div class="form-row">
                    <label>Body (DE)</label>
                    <textarea name="email_verify_body_de" rows="6">{{ $settings['email_verify_body_de'] ?? "Bitte klicken Sie auf die Schaltfläche unten, um Ihre E-Mail-Adresse zu bestätigen.\n\n{action_url}\n\nWenn Sie kein Konto erstellt haben, ist keine weitere Aktion erforderlich." }}</textarea>
                </div>
            </div>
            <div style="font-size:11px; color:var(--muted); margin-top:4px;">Available placeholders: {username}, {action_url}</div>
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

    function toggleStorageFields() {
        const driver = document.getElementById('storageDriverSelect').value;
        const s3 = document.getElementById('s3Settings');
        const ftp = document.getElementById('ftpSettings');

        s3.style.display = (driver === 's3') ? 'block' : 'none';
        ftp.style.display = (driver === 'ftp') ? 'block' : 'none';
    }

    function switchLang(type, lang) {
        // Toggle tabs
        document.getElementById('tab-' + type + '-en').classList.remove('active');
        document.getElementById('tab-' + type + '-de').classList.remove('active');
        document.getElementById('tab-' + type + '-' + lang).classList.add('active');

        // Toggle content
        document.getElementById('content-' + type + '-en').style.display = 'none';
        document.getElementById('content-' + type + '-de').style.display = 'none';
        document.getElementById('content-' + type + '-' + lang).style.display = 'block';
    }

    function loadDefaultReset(lang) {
        if(!confirm('Reset ' + lang.toUpperCase() + ' email template to default?')) return;
        
        if (lang === 'en') {
            document.querySelector('input[name="email_reset_subject_en"]').value = 'Reset Password Notification';
            document.querySelector('textarea[name="email_reset_body_en"]').value = "You are receiving this email because we received a password reset request for your account.\\n\\n{action_url}\\n\\nThis password reset link will expire in :count minutes.\\n\\nIf you did not request a password reset, no further action is required.";
        } else {
            document.querySelector('input[name="email_reset_subject_de"]').value = 'Passwort zurücksetzen';
            document.querySelector('textarea[name="email_reset_body_de"]').value = "Sie erhalten diese E-Mail, weil wir eine Anfrage zum Zurücksetzen des Passworts für Ihr Konto erhalten haben.\\n\\n{action_url}\\n\\nDieser Link zum Zurücksetzen des Passworts läuft in :count Minuten ab.\\n\\nWenn Sie kein Zurücksetzen des Passworts angefordert haben, ist keine weitere Aktion erforderlich.";
        }
    }

    function loadDefaultVerify(lang) {
        if(!confirm('Reset ' + lang.toUpperCase() + ' email template to default?')) return;

        if (lang === 'en') {
            document.querySelector('input[name="email_verify_subject_en"]').value = 'Verify Email Address';
            document.querySelector('textarea[name="email_verify_body_en"]').value = "Please click the button below to verify your email address.\\n\\n{action_url}\\n\\nIf you did not create an account, no further action is required.";
        } else {
            document.querySelector('input[name="email_verify_subject_de"]').value = 'E-Mail-Adresse bestätigen';
            document.querySelector('textarea[name="email_verify_body_de"]').value = "Bitte klicken Sie auf die Schaltfläche unten, um Ihre E-Mail-Adresse zu bestätigen.\\n\\n{action_url}\\n\\nWenn Sie kein Konto erstellt haben, ist keine weitere Aktion erforderlich.";
        }
    }

    // Initialize on load
    document.addEventListener('DOMContentLoaded', toggleStorageFields);
</script>
@endsection
