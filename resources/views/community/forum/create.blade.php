@extends('layouts.app')

@section('content')
<div class="home-centered-container" style="max-width:800px;">
    <div style="margin-bottom:16px;">
        <a href="{{ route('community.forum.category', $category) }}" style="font-size:12px; color:var(--muted); text-decoration:none;">&larr; Back to {{ $category->title }}</a>
        <h2 class="page-title" style="margin:4px 0 0; text-align:left;">Start New Thread</h2>
    </div>

    <div class="card">
        <form action="{{ route('community.forum.store', $category) }}" method="POST">
            @csrf
            <div class="form-row">
                <label>Title</label>
                <input type="text" name="title" required placeholder="Thread title" style="font-size:16px;">
            </div>
            
            <div class="form-row" style="margin-top:16px;">
                <label>Content</label>
                <textarea name="content" rows="10" required placeholder="Write your post content here..." style="font-size:14px;"></textarea>
            </div>

            <div style="margin-top:24px; text-align:right;">
                <button type="submit" class="btn">Create Thread</button>
            </div>
        </form>
    </div>
</div>
@endsection
