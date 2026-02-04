@extends('layouts.app')

@section('content')
  <h2>{{ $obj->name }} — {{ $obj->catalog }}</h2>
  <p>{{ $obj->description }}</p>

  <h3>Gallery</h3>
  <div class="grid">
    @forelse($images as $img)
      <div class="card">
        <img class="thumb" src="{{ Storage::url($img->path) }}" alt="{{ $img->filename }}">
        <div>{{ $img->user->name ?? '—' }}</div>
        <div>Scope: {{ $img->scopeModel->name ?? '—' }}</div>
        <div>Exp: {{ $img->exposure_total_seconds ?? '—' }}s</div>
      </div>
    @empty
      <p>No images yet for this object.</p>
    @endforelse
  </div>

  @auth
    <p><a href="{{ route('images.create') }}?object_id={{ $obj->id }}">Upload an image for this object</a></p>
    <p><a href="{{ route('compare.show', $obj->id) }}">Compare Dwarf vs Seestar</a></p>
  @endauth
@endsection
