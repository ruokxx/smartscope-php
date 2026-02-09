@extends('admin.layouts.app')

@section('admin-content')
  <h2>Users</h2>

  <div style="margin:12px 0">
    <form method="GET" style="display:flex;gap:8px;align-items:center">
      <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="search email or name" style="padding:8px;border-radius:6px;border:1px solid rgba(255,255,255,0.04);background:rgba(255,255,255,0.02);color:#e6eef6">
      <button class="btn" type="submit">Search</button>
    </form>
  </div>

  <div class="panel">
<style>
  @media (max-width: 900px) {
    .panel table, .panel thead, .panel tbody, .panel th, .panel td, .panel tr { display: block; }
    .panel thead tr { position: absolute; top: -9999px; left: -9999px; }
    .panel tr { border: 1px solid rgba(255,255,255,0.05); margin-bottom: 16px; border-radius: 8px; background: rgba(255,255,255,0.02); padding: 12px; }
    .panel td { border: none; border-bottom: 1px solid rgba(255,255,255,0.03); position: relative; padding: 8px 0 8px 40%; text-align: right; min-height: 40px; display: flex; align-items: center; justify-content: flex-end; }
    .panel td:before { position: absolute; top: 12px; left: 0; width: 35%; padding-right: 10px; white-space: nowrap; font-weight: bold; color: var(--muted); content: attr(data-label); text-align: left; }
    .panel td:last-child { border-bottom: 0; padding-bottom: 0; justify-content: flex-end; }
    .panel td.actions { justify-content: flex-end; gap: 4px; padding-left: 0; width: 100%; display:flex; flex-wrap:wrap; }
    .panel td.actions:before { display: none; } /* Hide label for actions to use full width */
  }
</style>

    <table>
      <thead>
        <tr>
          <th>ID</th><th>Name</th><th>Email</th><th>Verified</th><th>Role</th><th>Status</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $u)
          <tr>
            <td data-label="ID">{{ $u->id }}</td>
            <td data-label="Name"><span style="color:{{ $u->role_color }}">{{ $u->name }}</span></td>
            <td data-label="Email">{{ $u->email }}</td>
            <td data-label="Verified">
                @if($u->email_verified_at)
                    <span style="color:#2ecc71">Yes</span>
                @else
                    <span style="color:#e74c3c">No</span>
                @endif
            </td>
            <td data-label="Role">
                @if($u->is_admin) <span style="color:var(--accent); font-weight:bold;">Admin</span>
                @elseif($u->is_moderator) <span style="color:var(--accent2); font-weight:bold;">Mod</span>
                @else User @endif
            </td>
            <td data-label="Status">
                @if($u->banned_at) <span style="color:var(--danger); font-weight:bold;">Banned</span>
                @else <span style="color:#2ecc71">Active</span> @endif
            </td>
            <td class="actions" data-label="Actions">
              <a href="{{ route('admin.users.edit', $u->id) }}" class="btn" style="padding:4px 8px; font-size:12px;">Edit</a>
              
              <form action="{{ route('admin.users.toggle-moderator', $u->id) }}" method="POST" style="display:inline-block">
                  @csrf <button style="padding:4px 8px; font-size:12px; background:rgba(255,255,255,0.1); color:#fff;" title="Toggle Moderator">{{ $u->is_moderator ? 'Demote' : 'Promote' }}</button>
              </form>

              @if($u->banned_at)
                 <form action="{{ route('admin.users.unban', $u->id) }}" method="POST" style="display:inline-block">
                    @csrf <button style="padding:4px 8px; font-size:12px; background:var(--success); color:#000;">Unban</button>
                 </form>
              @else
                 <form action="{{ route('admin.users.ban', $u->id) }}" method="POST" style="display:inline-block">
                    @csrf <button style="padding:4px 8px; font-size:12px; background:var(--danger); color:#fff;" onclick="return confirm('Ban this user?')">Ban</button>
                 </form>
              @endif

              <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" style="display:inline-block">@csrf @method('DELETE')<button style="padding:4px 8px; font-size:12px; background:red; color:#fff;" onclick="return confirm('Delete?')">Del</button></form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div style="margin-top:12px">{{ $users->withQueryString()->links() }}</div>
  </div>
@endsection
