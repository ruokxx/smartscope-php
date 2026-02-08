@extends('layouts.app')

@section('content')
  <h2>{{ __('messages.upload_image') }}</h2>

  <form action="{{ route('images.store') }}" method="POST" enctype="multipart/form-data" class="upload-form">
    @csrf

    <div class="form-row">
      <label>{{ __('messages.image_file') }}</label>
      <input type="file" name="image" required />
    </div>

    <div class="form-row">
      <label>{{ __('messages.object') }} (optional)</label>
      <div class="styled-select-container">
        <select name="object_id" class="styled-select">
          <option value="">{{ __('messages.none') }}</option>
          @foreach($objects as $o)
            <option value="{{ $o->id }}" {{ request('object_id') == $o->id ? 'selected' : '' }}>
              {{ $o->name }} {{ $o->catalog ? '(' . $o->catalog . ')' : '' }}
            </option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="form-row">
      <label>{{ __('messages.scope') }}</label>
      <div class="styled-select-container">
        <select name="scope_id" class="styled-select">
          <option value="">{{ __('messages.none') }}</option>
          @foreach($scopes as $s)
            <option value="{{ $s->id }}" {{ old('scope_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="row-2">
      <div class="form-row">
        <label>{{ __('messages.sub_exposure') }}</label>
        <input type="number" step="0.1" name="sub_exposure_time" value="{{ old('sub_exposure_time') }}" placeholder="e.g. 10">
      </div>

      <div class="form-row">
        <label>{{ __('messages.number_of_subs') }}</label>
        <input type="number" name="number_of_subs" value="{{ old('number_of_subs') }}" placeholder="e.g. 120">
      </div>
    </div>

    <div class="row-2">
      <div class="form-row">
        <label>{{ __('messages.gain') }}</label>
        <input type="number" name="gain" value="{{ old('gain') }}" placeholder="e.g. 80 or 1600">
      </div>

      <div class="form-row">
        <label>{{ __('messages.filter') }}</label>
        <input type="text" name="filter" value="{{ old('filter') }}" placeholder="e.g. UHC, Dual-Band, None">
      </div>
    </div>

    <div class="row-2">
      <div class="form-row">
        <label>{{ __('messages.bortle') }}</label>
        <select name="bortle">
            <option value="">-- Select --</option>
            @foreach(range(1,9) as $b)
                <option value="{{ $b }}" {{ old('bortle') == $b ? 'selected' : '' }}>{{ __('messages.class') }} {{ $b }}</option>
            @endforeach
        </select>
      </div>

      <div class="form-row">
        <label>{{ __('messages.seeing') }}</label>
        <select name="seeing">
            <option value="">-- Select --</option>
            <option value="Excellent" {{ old('seeing') == 'Excellent' ? 'selected' : '' }}>{{ __('messages.seeing_excellent') }}</option>
            <option value="Good" {{ old('seeing') == 'Good' ? 'selected' : '' }}>{{ __('messages.seeing_good') }}</option>
            <option value="Average" {{ old('seeing') == 'Average' ? 'selected' : '' }}>{{ __('messages.seeing_average') }}</option>
            <option value="Poor" {{ old('seeing') == 'Poor' ? 'selected' : '' }}>{{ __('messages.seeing_poor') }}</option>
            <option value="Bad" {{ old('seeing') == 'Bad' ? 'selected' : '' }}>{{ __('messages.seeing_bad') }}</option>
        </select>
      </div>
    </div>

    <div class="form-row">
        <label>{{ __('messages.session_date') }}</label>
        <input type="date" name="session_date" value="{{ old('session_date') }}">
    </div>

    <div class="form-row">
      <label>{{ __('messages.exposure_total') }} <span style="font-size:12px;color:var(--muted)">{{ __('messages.exposure_hint') }}</span></label>
      <input type="number" name="exposure_total_seconds" value="{{ old('exposure_total_seconds') }}">
    </div>

    <div class="form-row">
      <label>{{ __('messages.processing_software') }}</label>
      <input type="text" name="processing_software" value="{{ old('processing_software') }}">
    </div>

    <div style="margin-top:12px">
      <button type="submit" class="btn">{{ __('messages.upload') }}</button>
    </div>
  </form>
@endsection
