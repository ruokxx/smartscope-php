@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 600px; margin: 0 auto; padding: 20px;">
    <div class="card">
        <h2 style="margin-top:0;">Create a New Group</h2>
        <form action="{{ route('groups.store') }}" method="POST">
            @csrf
            
            <div class="form-row">
                <label>Group Name</label>
                <input type="text" name="name" required placeholder="e.g. Astrophotography Beginners">
            </div>

            <div class="form-row">
                <label>Description</label>
                <textarea name="description" rows="4" placeholder="What is this group about?"></textarea>
            </div>

            <div style="margin-top:20px; text-align:right;">
                <a href="{{ route('community.index') }}" class="btn" style="background:transparent; border:1px solid var(--muted); color:var(--muted); margin-right:8px;">Cancel</a>
                <button type="submit" class="btn">Create Group</button>
            </div>
        </form>
    </div>
</div>
@endsection
