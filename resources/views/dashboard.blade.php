@extends('layouts.app')

@section('content')
  <h2>Dashboard</h2>
  <p>Willkommen, {{ auth()->user()->name }}!</p>

  <div style="margin-top:12px">
    <a href="{{ route('board') }}" style="margin-right:10px">Zur Sammlung (Board)</a>
    <a href="{{ route('images.create') }}" style="margin-right:10px">Bild hochladen</a>
    <a href="{{ route('profile.edit') }}">Profil bearbeiten</a>
  </div>

  <div style="margin-top:20px">
    <h3>Kurzübersicht</h3>
    <ul>
      <li>E-Mail: {{ auth()->user()->email }}</li>
      <li>Display name: {{ auth()->user()->display_name ?? '—' }}</li>
      <li>Vollständiger Name: {{ auth()->user()->full_name ?? '—' }}</li>
    </ul>
  </div>
@endsection
