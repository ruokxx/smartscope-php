@extends('layouts.app')

@section('content')
  <h2>Compare: {{ $object->name }}</h2>
  <div style="display:flex;gap:12px">
    <div class="card" style="flex:1;text-align:center">
      <h3>Dwarf</h3>
      @if($dwarf)
        <img class="thumb" src="{{ Storage::url($dwarf->path) }}">
        <div>Uploaded: {{ $dwarf->upload_time }}</div>
        <div>Software: {{ $dwarf->processing_software }}</div>
      @else
        <div class="muted">No Dwarf image</div>
      @endif
    </div>

    <div class="card" style="flex:1;text-align:center">
      <h3>Seestar</h3>
      @if($seestar)
        <img class="thumb" src="{{ Storage::url($seestar->path) }}">
        <div>Uploaded: {{ $seestar->upload_time }}</div>
        <div>Software: {{ $seestar->processing_software }}</div>
      @else
        <div class="muted">No Seestar image</div>
      @endif
    </div>
  </div>
@endsection
