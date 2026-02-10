@extends('layouts.app')

@section('content')
<div class="home-centered-container" style="max-width:600px; margin:0 auto; padding:20px;">
    <div style="margin-bottom:24px;">
        <a href="{{ route('community.index') }}" style="color:var(--muted); text-decoration:none;">&larr; Back to Community</a>
    </div>

    <div class="card p-4">
        <h2 style="margin-top:0;">Create a New Group</h2>
        <form action="{{ route('community.groups.store') }}" method="POST">
            @csrf
            
            <div class="form-row">
                <label>Group Name</label>
                <input type="text" name="name" required placeholder="e.g. Astrophotography Beginners" value="{{ old('name') }}">
                @error('name') <div style="color:red; font-size:12px;">{{ $message }}</div> @enderror
            </div>

            <div class="form-row">
                <label>Description</label>
                <textarea name="description" rows="4" placeholder="What is this group about?">{{ old('description') }}</textarea>
                @error('description') <div style="color:red; font-size:12px;">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary" style="background:var(--accent); color:#fff; border:none; padding:10px 20px; width:100%; border-radius:6px; font-weight:bold;">Create Group</button>
        </form>
    </div>
</div>
@endsection
