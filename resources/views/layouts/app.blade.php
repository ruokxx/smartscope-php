<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Smartscope Catalog</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <style>
    /* kleine Grund-Styles (falls du kein build hast) */
    body{font-family:Arial,Helvetica,sans-serif;margin:20px;color:#222}
    header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
    nav button, nav a{margin-left:8px;padding:6px 8px;border:1px solid #999;background:#f6f6f6}
    .grid{display:flex;flex-wrap:wrap;gap:12px}
    .card{border:1px solid #ddd;padding:12px;border-radius:6px;background:#fff}
    .muted{opacity:0.45}
    img.thumb{max-width:160px;max-height:120px;object-fit:cover}
  </style>
</head>
<body>
  <header>
    <div><h1><a href="{{ route('home') }}">Smartscope Catalog</a></h1></div>
    <nav>
      <a href="{{ route('home') }}">Home</a>
      <a href="{{ route('board') }}">Collection</a>
      @auth
        <a href="{{ route('images.create') }}">Upload</a>
        <a href="{{ route('profile.edit') }}">Profile</a>
        <form method="POST" action="{{ route('logout') }}" style="display:inline">@csrf<button>Logout</button></form>
      @else
        <a href="{{ route('login') }}">Log in</a>
        <a href="{{ route('register') }}">Register</a>
      @endauth
    </nav>
  </header>

  <main>
    @if(session('success'))<div style="padding:8px;background:#e6ffe6;border:1px solid #bfb">@{{ session('success') }}</div>@endif
    @yield('content')
  </main>
</body>
</html>
