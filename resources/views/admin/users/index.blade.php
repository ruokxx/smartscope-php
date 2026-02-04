@extends('admin.layouts.app')

@section('admin-content')
  <!-- bestehender Inhalt der Users-Index-View hier -->
@endsection


@section('content')
  <h2>Users</h2>
  <form method="GET"><input name="q" value="{{ $q }}" placeholder="search email or name"><button>Search</button></form>
  <table border="1" cellpadding="6" cellspacing="0">
    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Admin</th><th>Actions</th></tr></thead>
    <tbody>
      @foreach($users as $u)
      <tr>
        <td>{{ $u->id }}</td>
        <td>{{ $u->name }}</td>
        <td>{{ $u->email }}</td>
        <td>{{ $u->is_admin ? 'yes':'no' }}</td>
        <td>
          <a href="{{ route('admin.users.edit',$u->id) }}">Edit</a>
          <form method="POST" action="{{ route('admin.users.destroy',$u->id) }}" style="display:inline">@csrf @method('DELETE')<button onclick="return confirm('Delete?')">Delete</button></form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  {{ $users->withQueryString()->links() }}
@endsection
