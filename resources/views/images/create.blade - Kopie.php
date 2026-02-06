@extends('layouts.app')

@section('content')
  <h2>Upload Image</h2>
  <form action="{{ route('images.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div><label>Image file</label><br/><input type="file" name="image" required></div>
    <div><label>Object (optional)</label><br/>
      <select name="object_id">
        <option value="">-- none --</option>
        @foreach($objects as $o)
          <option value="{{ $o->id }}" {{ request('object_id') == $o->id ? 'selected' : '' }}>{{ $o->name }} ({{ $o->catalog }})</option>
        @endforeach
      </select>
    </div>
    <div><label>Scope</label><br/>
      <select name="scope_id">
        <option value="">-- none --</option>
        @foreach($scopes as $s)
          <option value="{{ $s->id }}">{{ $s->name }}</option>
        @endforeach
      </select>
    </div>
    <div><label>Exposure total (s)</label><br/><input type="number" name="exposure_total_seconds"></div>
    <div><label>Number of subs</label><br/><input type="number" name="number_of_subs"></div>
    <div><label>Processing software</label><br/><input type="text" name="processing_software"></div>
    <div style="margin-top:8px"><button type="submit">Upload</button></div>
  </form>
@endsection
