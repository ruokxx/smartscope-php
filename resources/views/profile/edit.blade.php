@extends('layouts.app')

@section('content')
  <h2>Profile</h2>
  <form method="POST" action="{{ route('profile.update') }}">
    @csrf
    <div><label>Name</label><br/><input name="name" value="{{ old('name',$user->name) }}"></div>
    <div><label>Display name</label><br/><input name="display_name" value="{{ old('display_name',$user->display_name ?? '') }}"></div>
    <div><label>Full name</label><br/><input name="full_name" value="{{ old('full_name',$user->full_name ?? '') }}"></div>
    <div><label>Email</label><br/><input name="email" value="{{ old('email',$user->email) }}"></div>
    <div><label>Twitter</label><br/><input name="twitter" value="{{ old('twitter',$user->twitter ?? '') }}"></div>
    <div><label>Instagram</label><br/><input name="instagram" value="{{ old('instagram',$user->instagram ?? '') }}"></div>
    <div><label>Homepage</label><br/><input name="homepage" value="{{ old('homepage',$user->homepage ?? '') }}"></div>
    <div style="margin-top:8px"><button type="submit">Save profile</button></div>
  </form>
@endsection
