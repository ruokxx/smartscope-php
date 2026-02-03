@extends('layouts.app')

@section('content')
  <h2>Your Collection / Board</h2>
  <p>Objects you own show your image; others are greyed out placeholders.</p>
  <div class="grid">
    @foreach($objects as $obj)
      <div class="card" style="width:220px;text-align:center">
        @if(isset($owned[$obj->id]))
          <img class="thumb" src="{{ Storage::url($owned[$obj->id]->path) }}" alt="{{ $obj->name }}">
        @else
          <div style="width:160px;height:120px;background:#eee;display:flex;align-items:center;justify-content:center;color:#999;margin:0 auto">
            <div class="muted">{{ $obj->name }}</div>
          </div>
        @endif
        <div style="margin-top:8px"><strong>{{ $obj->name }}</strong></div>
        <div>{{ $obj->catalog }}</div>
        <div style="margin-top:6px">
          <a href="{{ route('objects.show', $obj->id) }}">Details</a>
          @auth
            @if(!isset($owned[$obj->id]))
              &nbsp;|&nbsp;<a href="{{ route('images.create') }}?object_id={{ $obj->id }}">Upload</a>
            @endif
          @endauth
        </div>
      </div>
    @endforeach
  </div>
@endsection
