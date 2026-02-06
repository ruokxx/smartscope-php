@extends('admin.layouts.app')

@section('admin-content')
  <h2>Edit user #{{ $user->id }}</h2>

  <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
    @csrf
    @method('PUT')

    <div class="form-row">
      <label>Name</label>
      <input name="name" value="{{ old('name', $user->name) }}">
    </div>

    <div class="form-row">
      <label>Email</label>
      <input name="email" value="{{ old('email', $user->email) }}">
    </div>

<div class="form-row">
  <input type="hidden" name="is_admin" value="0">
  <label><input type="checkbox" name="is_admin" value="1" {{ $user->is_admin ? 'checked' : '' }}> Is admin</label>
</div>


    <div style="margin-top:12px">
      <button class="btn" type="submit">Save</button>
      <a href="{{ route('admin.users.index') }}" class="btn" style="background:transparent;border:1px solid rgba(255,255,255,0.04);color:var(--muted);margin-left:8px">Cancel</a>
    </div>
  </form>
@endsection
