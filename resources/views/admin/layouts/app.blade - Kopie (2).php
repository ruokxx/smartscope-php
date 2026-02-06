<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Admin • Smartscope</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <style>
    :root{--bg1:#07121b;--bg2:#071827;--panel:rgba(18,25,33,0.92);--muted:#9aa6b2;--accent:#6fb8ff;--accent2:#b27bff;--border:rgba(255,255,255,0.06);--glow:rgba(111,184,255,0.06);}
    html,body{height:100%;margin:0;font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,Arial;background:linear-gradient(180deg,var(--bg1),var(--bg2));color:#e6eef6}
    header{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:rgba(255,255,255,0.02);border-bottom:1px solid var(--border);backdrop-filter:blur(4px)}
    h1{margin:0;font-size:18px;color:var(--accent2);font-weight:700}
    nav{display:flex;gap:10px;align-items:center}
    nav a{padding:8px 12px;border-radius:8px;background:rgba(255,255,255,0.02);border:1px solid var(--border);color:var(--muted);text-decoration:none}
    nav a:hover{color:#fff;box-shadow:0 8px 24px var(--glow);transform:translateY(-2px)}
    .admin-btn{background:linear-gradient(90deg,var(--accent),var(--accent2));color:#041229;border:0;padding:8px 12px;border-radius:8px}
    main{max-width:1200px;margin:28px auto;padding:18px}
    .panel{background:var(--panel);border:1px solid var(--border);border-radius:12px;padding:16px;margin-bottom:16px}
    table{width:100%;border-collapse:collapse}
    table th, table td{padding:10px;border-bottom:1px solid rgba(255,255,255,0.03);text-align:left;color:#e6eef6}
    .thumb{max-width:140px;border-radius:8px;border:1px solid rgba(255,255,255,0.03)}
    .btn{padding:6px 10px;border-radius:6px}
    @media (max-width:900px){main{padding:12px}}
  </style>
</head>
<body>
  <header>
    <div><h1>Admin • Smartscope</h1></div>
    <nav>
      <a href="{{ route('admin.users.index') }}">Users</a>
      <a href="{{ route('admin.images.index') }}">Images</a>
      <a href="{{ route('admin.news.index') }}">News</a>
      <a href="{{ route('home') }}">Site</a>
    </nav>
  </header>

  <main>
    @if(session('success'))<div class="panel" style="background:rgba(111,184,255,0.04)">{{ session('success') }}</div>@endif

    @yield('admin-content')

  </main>
</body>
</html>
