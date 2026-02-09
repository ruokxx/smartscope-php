@extends('admin.layouts.app')

@section('admin-content')
  <h2>Users</h2>

  <div style="margin:12px 0">
    <form method="GET" style="display:flex;gap:8px;align-items:center">
      <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="search email or name" style="padding:8px;border-radius:6px;border:1px solid rgba(255,255,255,0.04);background:rgba(255,255,255,0.02);color:#e6eef6">
      <button class="btn" type="submit">Search</button>
      <a href="{{ route('admin.users.create') }}" class="btn" style="background:transparent;border:1px solid rgba(255,255,255,0.04);color:var(--muted);margin-left:8px">Create</a>
    </form>
  </div>

  <div class="panel">
    <table>
      <thead>
        <tr>
          <th>ID</th><th>Name</th><th>Email</th><th>Verified</th><th>Admin</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $u)
          <tr>
            <td>{{ $u->id }}</td>
            <td>{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td>
                @if($u->email_verified_at)
                    <span style="color:#2ecc71">Yes</span>
                @else
                    <span style="color:#e74c3c">No</span>
                @endif
            </td>
            <td>{{ $u->is_admin ? 'yes' : 'no' }}</td>
            <td class="actions">
              <a href="{{ route('admin.users.edit', $u->id) }}">Edit</a>
              <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button onclick="return confirm('Delete?')">Delete</button></form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div style="margin-top:12px">{{ $users->withQueryString()->links() }}</div>
  </div>
@endsection
