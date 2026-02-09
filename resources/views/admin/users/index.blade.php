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
          <th>ID</th><th>Name</th><th>Email</th><th>Verified</th><th>Role</th><th>Status</th><th>Actions</th>
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
            <td>
                @if($u->is_admin) <span style="color:var(--accent); font-weight:bold;">Admin</span>
                @elseif($u->is_moderator) <span style="color:var(--accent2); font-weight:bold;">Mod</span>
                @else User @endif
            </td>
            <td>
                @if($u->banned_at) <span style="color:var(--danger); font-weight:bold;">Banned</span>
                @else <span style="color:#2ecc71">Active</span> @endif
            </td>
            <td class="actions" style="display:flex; gap:4px; flex-wrap:wrap;">
              <a href="{{ route('admin.users.edit', $u->id) }}" class="btn" style="padding:4px 8px; font-size:12px;">Edit</a>
              
              <form action="{{ route('admin.users.toggle-moderator', $u->id) }}" method="POST">
                  @csrf <button style="padding:4px 8px; font-size:12px; background:rgba(255,255,255,0.1); color:#fff;" title="Toggle Moderator">{{ $u->is_moderator ? 'Demote' : 'Promote' }}</button>
              </form>

              @if($u->banned_at)
                 <form action="{{ route('admin.users.unban', $u->id) }}" method="POST">
                    @csrf <button style="padding:4px 8px; font-size:12px; background:var(--success); color:#000;">Unban</button>
                 </form>
              @else
                 <form action="{{ route('admin.users.ban', $u->id) }}" method="POST">
                    @csrf <button style="padding:4px 8px; font-size:12px; background:var(--danger); color:#fff;" onclick="return confirm('Ban this user?')">Ban</button>
                 </form>
              @endif

              <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST">@csrf @method('DELETE')<button style="padding:4px 8px; font-size:12px; background:red; color:#fff;" onclick="return confirm('Delete?')">Del</button></form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div style="margin-top:12px">{{ $users->withQueryString()->links() }}</div>
  </div>
@endsection
