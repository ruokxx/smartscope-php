<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🌙</text></svg>">
  <title>{{ __('messages.site_title') }}</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <style>
    :root{
      --bg1:#07121b;
      --bg2:#071827;
      --panel:rgba(18,25,33,0.9);
      --muted:#9aa6b2;
      --accent:#6fb8ff;
      --accent2:#b27bff;
      --border:rgba(255,255,255,0.06);
      --glow:rgba(111,184,255,0.06);
    }

    html,body{
      min-height:100vh;
      margin:0;
      font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,Arial,Helvetica;
      background: linear-gradient(180deg,var(--bg1) 0%, var(--bg2) 100%);
      background-attachment: fixed;
      color:#e6eef6;
      -webkit-font-smoothing:antialiased;
    }
    *, *::before, *::after {
      box-sizing: border-box;
    }

    body::before{
      content:'';
      position:fixed;
      inset:0;
      background-image:
        radial-gradient(rgba(255,255,255,0.9) 1px, transparent 1px),
        radial-gradient(rgba(255,255,255,0.7) 1px, transparent 1px);
      background-size: 500px 500px, 1200px 1200px;
      opacity:0.03;
      pointer-events:none;
      z-index:0;
    }

    header{
      position:relative;
      z-index:2;
      display:flex;
      justify-content:space-between;
      align-items:center;
      padding:16px 24px;
      background: rgba(255,255,255,0.02);
      border-bottom:1px solid var(--border);
      backdrop-filter: blur(4px);
    }

    h1{margin:0;font-size:20px;font-weight:700}
    /* shimmer animation moved to shared block below */
    @keyframes shimmer {
      0% { background-position: 0% center; }
      100% { background-position: 200% center; }
    }

    nav{display:flex;gap:10px;align-items:center;z-index:2}
    nav a, nav button{
      padding:7px 10px;
      border-radius:8px;
      background: rgba(255,255,255,0.02);
      border:1px solid var(--border);
      color:var(--muted);
      text-decoration:none;
      font-size:14px;
      transition: transform .12s ease, box-shadow .12s ease;
    }
    nav a:hover, nav button:hover{
      transform:translateY(-3px);
      color:#fff;
      box-shadow: 0 8px 24px var(--glow);
    }

    .admin-btn{
      background: linear-gradient(90deg,var(--accent),var(--accent2));
      color:#041229;
      border:none;
      box-shadow: 0 8px 30px var(--glow);
      font-weight:700;
    }

    main{
      position:relative;
      z-index:1;
      max-width:1100px;
      margin:32px auto;
      padding:0 18px 80px;
    }

    .grid{display:flex;flex-wrap:wrap;gap:18px}
    .card{
      background: rgba(255,255,255,0.02);
      border:1px solid var(--border);
      border-radius:12px;
      padding:16px;
      box-shadow: 0 6px 18px rgba(2,6,23,0.5);
      color:#e6eef6;
    }

    .card.thumb-small{width:260px}
    .card.full{width:100%}

    .muted{color:var(--muted)}
    img.thumb{max-width:100%;height:auto;border-radius:8px;display:block;border:1px solid rgba(255,255,255,0.03)}

    .accent-line{height:6px;border-radius:8px;background:linear-gradient(90deg,var(--accent),var(--accent2));margin-bottom:12px}

    /* form controls */
    input[type="text"], input[type="email"], input[type="number"], textarea {
      width:100%; padding:10px 12px; border-radius:8px; border:1px solid rgba(255,255,255,0.04);
      background: rgba(255,255,255,0.02); color:#e6eef6; margin-top:8px; margin-bottom:12px; box-sizing:border-box;
    }

    h1 a, .shimmer-text, h2, h3, .team-member-name {
      background: linear-gradient(90deg, #e6eef6, var(--accent), #e6eef6);
      background-size: 200% auto;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      text-decoration:none;
      animation: shimmer 3s linear infinite;
    }

    button, .btn {
      padding:8px 12px; border-radius:8px;
      /* Background shimmer for buttons (so they are visible) */
      background: linear-gradient(90deg, var(--accent), var(--accent2), var(--accent));
      background-size: 200% auto;
      animation: shimmer 3s linear infinite;
      
      color:#041229; border:0; cursor:pointer; font-weight:700;
    }

    /* Keep pagination colors distinct */
    .pagination .active span, .pagination .active a {
      background: linear-gradient(90deg,var(--accent),var(--accent2),var(--accent));
      background-size: 200% auto;
      animation: shimmer 3s linear infinite;
      color:#041229; border-color:transparent; font-weight:700;
    }
    .pagination a:hover { background: rgba(255,255,255,0.03); color:#fff; transform:translateY(-2px); }

    /* Styled select control + chevron */
    .styled-select-container { position: relative; display: inline-block; width:100%; max-width:420px; }
    .styled-select {
      appearance: none; -webkit-appearance: none; -moz-appearance: none;
      width: 100%; padding: 10px 40px 10px 12px; border-radius: 8px;
      border: 1px solid rgba(255,255,255,0.04); background: rgba(255,255,255,0.02);
      color: #e6eef6; box-sizing: border-box; font-size: 14px;
    }
    .styled-select-container::after {
      content: ""; position: absolute; right: 12px; top: 50%; transform: translateY(-50%) rotate(45deg);
      width: 10px; height: 10px; border-right: 2px solid rgba(230,238,246,0.9); border-bottom: 2px solid rgba(230,238,246,0.9);
      pointer-events: none; opacity: 0.9;
    }

    /* Mobile Menu Styles */
    .mobile-menu-btn { display: none; background: transparent; border: none; color: #fff; font-size: 24px; cursor: pointer; z-index: 20; padding: 8px; }
    .mobile-menu {
        position: fixed; inset: 0; background: rgba(7, 18, 27, 0.95); backdrop-filter: blur(10px); z-index: 15;
        display: none; flex-direction: column; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease;
    }
    .mobile-menu.active { display: flex; opacity: 1; }
    .mobile-menu a, .mobile-menu button {
        background: transparent; border: none; color: #fff; font-size: 18px; margin: 12px 0; text-decoration: none; padding: 10px 20px; border-radius: 8px;
        transition: background 0.2s; text-align: center; width: 100%; max-width: 250px;
    }
    .mobile-menu a.logout-btn { color: #ff6b6b; }
    .mobile-menu a:hover, .mobile-menu button:hover { background: rgba(255,255,255,0.1); }

.site-footer {
  margin-top: 40px;
  padding: 18px 0;
  text-align: center;
  color: var(--muted);
  font-size: 13px;
  border-top: 1px solid rgba(255,255,255,0.03);
  background: linear-gradient(180deg, rgba(255,255,255,0.00), rgba(255,255,255,0.005));
}
.site-footer .container { max-width:1100px; margin:0 auto; padding:0 18px; }

/* Upload form layout */
.upload-form { max-width:760px; margin:0 auto; }
.form-row { margin-bottom:14px; display:block; }
.form-row label { display:block; margin-bottom:6px; color:var(--muted); font-size:14px; }
.upload-form input[type="file"] { width:100%; padding:8px; background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.04); border-radius:8px; color:#e6eef6; }
.upload-form input[type="text"], .upload-form input[type="number"], .upload-form textarea { width:100%; }
.styled-select-container { width:100%; }

/* optional: two-column layout on wide screens for numeric fields */
@media (min-width:900px){
  .upload-form .row-2 { display:flex; gap:12px; }
  .upload-form .row-2 .form-row { flex:1; margin-bottom:14px; }
}
    .styled-select:focus { outline: none; box-shadow: 0 6px 18px rgba(111,184,255,0.06); border-color: rgba(127,182,255,0.4); }
    .styled-select option { background: #0f1724; color: #e6eef6; }

    @media (max-width:900px){
      .grid{flex-direction:column;align-items:stretch}
      header{padding:12px 18px;}
      nav.desktop-nav { display: none; }
      .mobile-menu-btn { display: block; }

      /* Mobile nav layout */
      .mobile-menu a, .mobile-menu button { font-size: 18px; padding: 12px; }
      .header-description { display: none; }
    }
    
    .header-description {
        flex:1; 
        text-align:center; 
        padding:0 24px; 
        color:var(--muted); 
        font-size:13px; 
        font-weight:500;
        display:block;
    }
    @media (max-width: 900px) {
        .header-description { display: none; }
    }
  </style>
</head>
<body style="background-color: var(--bg1);"> <!-- Ensure background color is set to avoid white footer -->
  <header>
    <div style="z-index: 21;">
        <h1><a href="{{ route('home') }}">{{ __('messages.site_title') }}</a></h1>
        <div class="shimmer-text" style="font-size:11px; font-weight:600; margin-top:0px;">Vergleiche und Entscheide ! Welches Smart Scope ?</div>
    </div>

    <!-- Site Description -->
    <div class="header-description">
        {{ $settings['header_description'] ?? __('messages.header_description') }}
    </div>
    
    <!-- Desktop Nav -->
    <nav class="desktop-nav">
      {{-- Language Switcher --}}
      <div style="display:flex; gap:4px; margin-right:8px;">
          <a href="{{ route('lang.switch', 'en') }}" style="padding:4px 8px; font-size:12px; {{ app()->getLocale() == 'en' ? 'background:rgba(111,184,255,0.2);color:#fff;' : '' }}">EN</a>
          <a href="{{ route('lang.switch', 'de') }}" style="padding:4px 8px; font-size:12px; {{ app()->getLocale() == 'de' ? 'background:rgba(111,184,255,0.2);color:#fff;' : '' }}">DE</a>
      </div>

      <a href="{{ route('home') }}">{{ __('messages.home') }}</a>
      <a href="{{ route('board') }}">{{ __('messages.collection') }}</a>



      @auth
        <a href="{{ route('images.create') }}">{{ __('messages.upload') }}</a>
        @if(\App\Models\Setting::where('key', 'community_enabled')->value('value') !== '0')
            <a href="{{ route('community.index') }}">{{ __('messages.community') }}</a>
        @endif
        <a href="{{ route('profile.edit') }}">Profile</a>

        @if((auth()->user()->is_admin ?? false) || (auth()->user()->is_moderator ?? false))
          <a href="{{ route('admin.moderation.index') }}" class="admin-btn" style="background:var(--accent2); color:#fff;">Mod</a>
        @endif
        @if(auth()->user()->is_admin ?? false)
            <a href="{{ route('admin.users.index') }}" class="admin-btn">{{ __('messages.admin') }}</a>
        @endif

        <form method="POST" action="{{ route('logout') }}" style="display:inline;margin:0">
          @csrf
          <button type="submit">{{ __('messages.logout') }}</button>
        </form>
      @else
        <a href="#" onclick="event.preventDefault(); openAuthModal('login')">{{ __('messages.login') }}</a>
        <a href="#" onclick="event.preventDefault(); openAuthModal('register')">{{ __('messages.register') }}</a>
      @endauth
    </nav>

    <!-- Hamburger Button -->
    <button class="mobile-menu-btn" onclick="toggleMobileMenu()">☰</button>
  </header>

  <!-- Mobile Menu Overlay -->
  <div id="mobile-menu" class="mobile-menu">
      <h2 style="color:var(--muted); font-size:14px; text-transform:uppercase; letter-spacing:1px; margin-bottom:24px;">Menu</h2>
      
      <a href="{{ route('home') }}" onclick="toggleMobileMenu()">{{ __('messages.home') }}</a>
      <a href="{{ route('board') }}" onclick="toggleMobileMenu()">{{ __('messages.collection') }}</a>

      @auth
        <a href="{{ route('images.create') }}" onclick="toggleMobileMenu()">{{ __('messages.upload') }}</a>
        @if(\App\Models\Setting::where('key', 'community_enabled')->value('value') !== '0')
            <a href="{{ route('community.index') }}" onclick="toggleMobileMenu()">{{ __('messages.community') }}</a>
        @endif
        <a href="{{ route('profile.edit') }}" onclick="toggleMobileMenu()">Profile</a>

        @if((auth()->user()->is_admin ?? false) || (auth()->user()->is_moderator ?? false))
          <a href="{{ route('admin.moderation.index') }}" class="admin-btn" style="background:var(--accent2); color:#fff;" onclick="toggleMobileMenu()">Mod Queue</a>
        @endif
        @if(auth()->user()->is_admin ?? false)
            <a href="{{ route('admin.users.index') }}" onclick="toggleMobileMenu()" style="color:var(--accent);">{{ __('messages.admin') }}</a>
        @endif

        <form method="POST" action="{{ route('logout') }}" style="width:100%; display:flex; justify-content:center;">
          @csrf
          <button type="submit" class="logout-btn">{{ __('messages.logout') }}</button>
        </form>
      @else
        <a href="#" onclick="event.preventDefault(); toggleMobileMenu(); openAuthModal('login')">{{ __('messages.login') }}</a>
        <a href="#" onclick="event.preventDefault(); toggleMobileMenu(); openAuthModal('register')">{{ __('messages.register') }}</a>
      @endauth

      <!-- Mobile Language Switcher -->
      <div style="margin-top:24px; display:flex; gap:12px;">
          <a href="{{ route('lang.switch', 'en') }}" style="width:auto; {{ app()->getLocale() == 'en' ? 'color:var(--accent);border-bottom:1px solid var(--accent);' : 'color:var(--muted);' }}">English</a>
          <a href="{{ route('lang.switch', 'de') }}" style="width:auto; {{ app()->getLocale() == 'de' ? 'color:var(--accent);border-bottom:1px solid var(--accent);' : 'color:var(--muted);' }}">Deutsch</a>
      </div>
  </div>

  <main>
    @if(session('success'))
      <div class="notice">{{ session('success') }}</div>
    @endif

  @yield('content')
    
    <footer class="site-footer">
      <div class="container">
        <span class="shimmer-text">Smart Teleskop Astrofoto Beta V2 • © Sebastian Thielke 2026</span>
        <a href="https://www.facebook.com/T4hund3R/" target="_blank" style="color:#9aa6b2; text-decoration:none; vertical-align:middle; margin-left:10px; display:inline-flex; align-items:center;" title="Facebook">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
            <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/>
          </svg>
        </a>
        <br>
        <div style="display:flex; justify-content:center; align-items:center; gap:24px; margin-top:12px;">
            <!-- Simple text stats -->
            <span class="shimmer-text" style="font-size:11px; opacity:0.7; font-weight:bold;">
                {{ __('messages.stats_images', ['count' => $global_stats['images_count'] ?? 0]) }} • 
                {{ __('messages.stats_users', ['count' => $global_stats['users_count'] ?? 0]) }}
            </span>

            <!-- Disk Usage Chart -->
            <div style="display:flex; align-items:center; gap:10px;" title="{{ __('messages.stats_disk', ['used' => $global_stats['used_gb'] ?? 0, 'free' => $global_stats['free_gb'] ?? 0]) }}">
                <div style="
                    position:relative;
                    width:40px; height:40px;
                    border-radius:50%;
                    background: conic-gradient(var(--accent) {{ $global_stats['used_percent'] ?? 0 }}%, rgba(255,255,255,0.1) 0);
                    display:flex; align-items:center; justify-content:center;
                ">
                    <!-- Inner circle for donut effect -->
                    <div style="
                        position:absolute; inset:4px;
                        background:var(--bg2); /* Matches footer background approx */
                        border-radius:50%;
                    "></div>
                    
                    <!-- Percentage Text -->
                    <span style="position:relative; z-index:1; font-size:10px; font-weight:bold; color:var(--muted);">
                        {{ $global_stats['used_percent'] ?? 0 }}%
                    </span>
                </div>
                <div style="text-align:left; font-size:10px; color:var(--muted); opacity:0.8;">
                    <div>{{ $global_stats['used_gb'] ?? 0 }} GB</div>
                    <div>Used</div>
                </div>
            </div>
        </div>
      </div>
    </footer>
  </main>
  @include('partials.auth-modal')
  @include('partials.verification-modal')

  <script>
      function toggleMobileMenu() {
          const menu = document.getElementById('mobile-menu');
          const btn = document.querySelector('.mobile-menu-btn');
          if (menu.classList.contains('active')) {
              menu.classList.remove('active');
              btn.innerHTML = '☰';
              document.body.style.overflow = '';
          } else {
              menu.classList.add('active');
              btn.innerHTML = '✕';
              document.body.style.overflow = 'hidden';
          }
      }
  </script>
</body>
</html>
