<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Admin • Smartscope</title>
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
    *, *::before, *::after { box-sizing: border-box; }
    
    body::before{
      content:''; position:fixed; inset:0;
      background-image: radial-gradient(rgba(255,255,255,0.9) 1px, transparent 1px), radial-gradient(rgba(255,255,255,0.7) 1px, transparent 1px);
      background-size: 500px 500px, 1200px 1200px; opacity:0.03; pointer-events:none; z-index:0;
    }

    /* Container and Layout */
    .admin-wrapper { display:flex; min-height:100vh; }
    
    /* Sidebar */
    aside {
      width:240px; background:rgba(18,25,33,0.95); border-right:1px solid var(--border);
      display:flex; flex-direction:column; position:fixed; top:0; bottom:0; padding:20px; z-index:100;
    }
    .sidebar-header h1 { font-size:18px; margin-bottom:24px; color:#fff; }
    aside nav { display:flex; flex-direction:column; gap:8px; flex:1; }
    aside nav a {
      display:block; padding:10px 14px; border-radius:8px; color:var(--muted); text-decoration:none;
      transition:all 0.2s; border:1px solid transparent; font-size:14px;
    }
    aside nav a:hover, aside nav a.active {
      background:rgba(255,255,255,0.05); color:#fff; border-color:rgba(255,255,255,0.1);
    }
    .sidebar-footer { margin-top:auto; padding-top:20px; border-top:1px solid var(--border); }
    .btn-exit { display:block; text-align:center; color:var(--muted); text-decoration:none; font-size:13px; }
    .btn-exit:hover { color:#fff; }

    /* Main Content */
    main { flex:1; margin-left:240px; padding:32px; width:calc(100% - 240px); }

    /* Mobile Header (Hidden on Desktop) */
    .mobile-header { display:none; padding:12px 16px; background:var(--bg2); border-bottom:1px solid var(--border); align-items:center; justify-content:space-between; position:sticky; top:0; z-index:90; }
    .menu-btn { background:none; border:none; color:#fff; font-size:24px; cursor:pointer; }
    #adminOverlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:99; }

    /* Responsive */
    @media (max-width: 900px) {
      .admin-wrapper { flex-direction:column; }
      aside { transform:translateX(-100%); transition:transform 0.3s ease; }
      aside.active { transform:translateX(0); }
      main { margin-left:0; width:100%; padding:16px; }
      .mobile-header { display:flex; }
      #adminOverlay.active { display:block; }
    }

    /* Common Components */
    .panel, .card {
      background: rgba(255,255,255,0.02); border:1px solid var(--border); border-radius:12px; padding:16px;
      box-shadow: 0 6px 18px rgba(2,6,23,0.5); color:#e6eef6; margin-bottom:16px;
    }
    .card.full { width:100%; }
    table{width:100%;border-collapse:collapse}
    table th, table td{padding:12px;border-bottom:1px solid rgba(255,255,255,0.03);text-align:left;color:#e6eef6}
    table th { color:var(--muted); font-size:12px; text-transform:uppercase; letter-spacing:0.5px; }
    
    .btn { padding:6px 12px; border-radius:6px; background: linear-gradient(90deg,var(--accent),var(--accent2)); color:#041229; border:none; cursor:pointer; font-weight:700; font-size:13px; text-decoration:none; display:inline-block; }
    .btn:hover { opacity:0.9; }

    .notice { padding:10px;border-radius:8px;background:rgba(111,184,255,0.06);border:1px solid rgba(111,184,255,0.06);color:#e6eef6;margin-bottom:14px }
    
     input[type="text"], input[type="email"], input[type="number"], textarea, select {
        width:100%; padding:8px 12px; border-radius:8px; border:1px solid rgba(255,255,255,0.04);
        background: rgba(255,255,255,0.02); color:#e6eef6; box-sizing:border-box; margin-top:4px; margin-bottom:12px;
     }

     /* Form Layout Utilities */
     .upload-form { max-width:800px; margin:0 auto; }
     .form-row { margin-bottom:14px; display:block; }
     .form-row label { display:block; margin-bottom:6px; color:var(--muted); font-size:13px; }
     .row-2 { display:flex; gap:16px; }
     .row-2 .form-row { flex:1; }
     
     .styled-select-container { position: relative; width:100%; }
     .styled-select {
       appearance: none; -webkit-appearance: none; width: 100%; padding: 8px 12px; border-radius: 8px;
       border: 1px solid rgba(255,255,255,0.04); background: rgba(255,255,255,0.02); color: #e6eef6;
     }
     .styled-select option { background:#07121b; } 
     .styled-select option { background:#07121b; } 
     .accent-line { height:1px; background:rgba(255,255,255,0.06); margin:20px 0; }

     /* Fix Laravel Pagination Arrows */
     nav[role="navigation"] svg { width:20px; height:20px; }
     nav[role="navigation"] .hidden { display:none; }

     /* Custom Pagination Styling */
     .pagination { display:flex; list-style:none; padding:0; gap:4px; }
     .page-item .page-link {
        display:flex; align-items:center; justify-content:center;
        padding:8px 12px; border-radius:6px; background:rgba(255,255,255,0.05); color:var(--muted);
        text-decoration:none; font-size:13px; transition:all 0.2s; min-width:32px; height:32px;
     }
     .page-item.active .page-link { background:var(--accent); color:#fff; font-weight:bold; }
     .page-item.disabled .page-link { opacity:0.5; pointer-events:none; }
     .page-item:not(.active):not(.disabled) .page-link:hover { background:rgba(255,255,255,0.1); color:#fff; }
  </style>
</head>
<body>
  <div class="admin-wrapper">
    <!-- Mobile Header -->
    <header class="mobile-header">
      <button class="menu-btn" onclick="toggleAdminMenu()">☰</button>
      <h1><a href="{{ route('admin.users.index') }}">Admin</a></h1>
      <a href="{{ route('home') }}" class="exit-link">Exit</a>
    </header>

    <!-- Sidebar Navigation -->
    <aside id="adminSidebar">
      <div class="sidebar-header">
         <h1>Smartscope Admin</h1>
      </div>
      <nav>
        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <span>Users</span>
        </a>
        <a href="{{ route('admin.moderation.index') }}" class="{{ request()->routeIs('admin.moderation.*') ? 'active' : '' }}" style="color:var(--accent2);">
            <span>Moderation</span>
        </a>
        <a href="{{ route('admin.images.index') }}" class="{{ request()->routeIs('admin.images.*') ? 'active' : '' }}">
            <span>Images</span>
        </a>
        <a href="{{ route('admin.objects.index') }}" class="{{ request()->routeIs('admin.objects.*') ? 'active' : '' }}">
            <span>Objects</span>
        </a>
        <a href="{{ route('admin.forum.categories.index') }}" class="{{ request()->routeIs('admin.forum.*') ? 'active' : '' }}">
            <span>Forum</span>
        </a>
        <a href="{{ route('admin.news.index') }}" class="{{ request()->routeIs('admin.news.*') ? 'active' : '' }}">
            <span>News</span>
        </a>
        <a href="{{ route('admin.changelogs.index') }}" class="{{ request()->routeIs('admin.changelogs.*') ? 'active' : '' }}">
            <span>Changelogs</span>
        </a>
        <a href="{{ route('admin.groups.index') }}" class="{{ request()->routeIs('admin.groups.*') ? 'active' : '' }}">
            <span>Groups</span>
        </a>
        <a href="{{ route('admin.community.index') }}" class="{{ request()->routeIs('admin.community.*') ? 'active' : '' }}">
            <span>Community</span>
        </a>
        <a href="{{ route('admin.backups.index') }}" class="{{ request()->routeIs('admin.backups.*') ? 'active' : '' }}">
            <span>Backups</span>
        </a>
        <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <span>Settings</span>
        </a>
      </nav>
      <div class="sidebar-footer">
        <a href="{{ route('home') }}" class="btn-exit">← Back to Site</a>
      </div>
    </aside>

    <!-- Main Content -->
    <main>
      @if(session('success'))<div class="notice">{{ session('success') }}</div>@endif
      @yield('admin-content')
    </main>

    <!-- Overlay for mobile -->
    <div id="adminOverlay" onclick="toggleAdminMenu()"></div>
  </div>

  <script>
    function toggleAdminMenu() {
      document.getElementById('adminSidebar').classList.toggle('active');
      document.getElementById('adminOverlay').classList.toggle('active');
    }
  </script>
</body>
</html>
