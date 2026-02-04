@extends('admin.layouts.app')
@section('content')
  <h2>Edit user #{{ $user->id }}</h2>
  <form method="POST" action="{{ route('admin.users.update',$user->id) }}">
    @csrf @method('PUT')
    <div>Name: <input name="name" value="{{ old('name',$user->name) }}"></div>
    <div>Email: <input name="email" value="{{ old('email',$user->email) }}"></div>
    <div>Is admin: <input type="checkbox" name="is_admin" value="1" {{ $user->is_admin ? 'checked':'' }}></div>
    <div><button type="submit">Save</button></div>
  </form>
@endsection
