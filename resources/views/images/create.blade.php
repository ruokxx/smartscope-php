@extends('layouts.app')

@section('content')
  <h2>Upload Image</h2>

  <form action="{{ route('images.store') }}" method="POST" enctype="multipart/form-data" class="upload-form">
    @csrf

    <div class="form-row">
      <label>Image file</label>
      <input type="file" name="image" required />
    </div>

    <div class="form-row">
      <label>Object (optional)</label>
      <div class="styled-select-container">
        <select name="object_id" class="styled-select">
          <option value="">-- none --</option>
          @foreach($objects as $o)
            <option value="{{ $o->id }}" {{ request('object_id') == $o->id ? 'selected' : '' }}>
              {{ $o->name }} {{ $o->catalog ? '(' . $o->catalog . ')' : '' }}
            </option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="form-row">
      <label>Scope</label>
      <div class="styled-select-container">
        <select name="scope_id" class="styled-select">
          <option value="">-- none --</option>
          @foreach($scopes as $s)
            <option value="{{ $s->id }}" {{ old('scope_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="row-2">
      <div class="form-row">
        <label>Exposure total (s)</label>
        <input type="number" name="exposure_total_seconds" value="{{ old('exposure_total_seconds') }}">
      </div>

      <div class="form-row">
        <label>Number of subs</label>
        <input type="number" name="number_of_subs" value="{{ old('number_of_subs') }}">
      </div>
    </div>

    <div class="form-row">
      <label>Processing software</label>
      <input type="text" name="processing_software" value="{{ old('processing_software') }}">
    </div>

    <div style="margin-top:12px">
      <button type="submit" class="btn">Upload</button>
    </div>
  </form>
@endsection
