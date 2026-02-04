<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Smartscope Catalog</title>
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
      height:100%;
      margin:0;
      font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,Arial,Helvetica;
      background: linear-gradient(180deg,var(--bg1) 0%, var(--bg2) 100%);
      color:#e6eef6;
      -webkit-font-smoothing:antialiased;
    }

    header{
      position:relative;
      z-index:2;
      display:flex;
      justify-content:space-between;
      align-items:center;
      padding:14px 20px;
      background: rgba(255,255,255,0.02);
      border-bottom:1px solid var(--border);
      backdrop-filter: blur(4px);
    }

    h1{margin:0;font-size:20px;color:var(--accent2);font-weight:700}
    h1 a{color:var(--accent2);text-decoration:none}

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

    form input, form select, form textarea{
      width:100%;
      padding:10px 12px;
      border-radius:8px;
      border:1px solid rgba(255,255,255,0.04);
      background: rgba(255,255,255,0.02);
      color:#e6eef6;
      margin-top:8px;
      margin-bottom:12px;
    }

    button, .btn {
      padding:8px 12px;
      border-radius:8px;
      background: linear-gradient(90deg,var(--accent),var(--accent2));
      color:#041229;
      border:0;
      cursor:pointer;
      font-weight:700;
    }

    .notice { padding:10px;border-radius:8px;background:rgba(111,184,255,0.06);border:1px solid rgba(111,184,255,0.06);color:#e6eef6;margin-bottom:14px }

    @media (max-width:900px){
      .grid{flex-direction:column;align-items:stretch}
      header{padding:12px}
    }
  </style>
</head>
<body>
  <header>
    <div><h1><a href="{{ route('home') }}">Smartscope Catalog</a></h1></div>
    <nav>
      <a href="{{ route('home') }}">Home</a>
      <a href="{{ route('board') }}">Collection</a>

      @auth
        @if(auth()->user()->is_admin ?? false)
          <a href="{{ route('admin.users.index') }}" class="admin-btn">Admin</a>
        @endif

        <a href="{{ route('images.create') }}">Upload</a>
        <a href="{{ route('profile.edit') }}">Profile</a>

        <form method="POST" action="{{ route('logout') }}" style="display:inline;margin:0">
          @csrf
          <button type="submit">Logout</button>
        </form>
      @else
        <a href="{{ route('login') }}">Log in</a>
        <a href="{{ route('register') }}">Register</a>
      @endauth
    </nav>
  </header>

  <main>
    @if(session('success'))
      <div class="notice">{{ session('success') }}</div>
    @endif

    @yield('content')
  </main>
</body>
</html>
