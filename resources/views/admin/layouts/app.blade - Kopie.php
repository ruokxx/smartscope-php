<!doctype html>
<html><head><meta charset="utf-8"><title>Admin - Smartscope</title>
<link rel="stylesheet" href="{{ asset('css/app.css') }}"></head><body>
<header style="padding:10px;background:#f4f4f4">
  <a href="{{ route('admin.users.index') }}">Users</a> |
  <a href="{{ route('admin.images.index') }}">Images</a> |
  <a href="{{ route('home') }}">Site</a>
</header>
<main style="padding:12px">
  @if(session('success'))<div style="background:#e6ffe6;padding:8px">{{ session('success') }}</div>@endif
  @yield('content')
</main>
</body></html>
