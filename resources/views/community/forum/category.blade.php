@extends('layouts.app')

@section('content')
<div class="home-centered-container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <div>
            <a href="{{ route('community.forum.index') }}" style="font-size:12px; color:var(--muted); text-decoration:none;">&larr; Back to Forums</a>
            <h2 class="page-title" style="margin:4px 0 0; text-align:left;">{{ $category->title }}</h2>
            @if($category->description)
                <p style="margin:4px 0 0; font-size:13px; color:var(--muted);">{{ $category->description }}</p>
            @endif
        </div>
        <a href="{{ route('community.forum.create', $category) }}" class="btn">New Thread</a>
    </div>

    @if($category->children()->count() > 0)
        <div style="margin-bottom:24px;">
            <h3 style="font-size:14px; color:var(--muted); margin-bottom:8px;">Subcategories</h3>
            <div class="card" style="padding:0; overflow:hidden;">
                @foreach($category->children as $child)
                    <div style="padding:12px 16px; border-bottom:1px solid rgba(255,255,255,0.05); display:flex; justify-content:space-between; align-items:center;">
                         <div>
                            <div style="font-weight:600;"><a href="{{ route('community.forum.category', $child) }}" style="color:#e6eef6; text-decoration:none;">{{ $child->title }}</a></div>
                            @if($child->description)
                                <div style="font-size:11px; color:var(--muted);">{{ $child->description }}</div>
                            @endif
                         </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="card" style="padding:0; overflow:hidden;">
        @forelse($threads as $thread)
            <div style="padding:16px; border-bottom:1px solid rgba(255,255,255,0.05); display:flex; align-items:center; gap:16px;">
                <div style="width:40px; text-align:center;">
                    <div style="font-size:18px; color:{{ $thread->is_pinned ? 'var(--accent)' : 'rgba(255,255,255,0.2)' }};">
                        {{ $thread->is_locked ? '🔒' : ($thread->is_pinned ? '📌' : '📄') }}
                    </div>
                </div>
                <div style="flex:1;">
                    <div style="font-size:15px; font-weight:600; margin-bottom:4px;">
                        <a href="{{ route('community.forum.thread', $thread) }}" style="color:#e6eef6; text-decoration:none;">{{ $thread->title }}</a>
                    </div>
                    <div style="font-size:12px; color:var(--muted);">
                        Started by <span style="color:{{ $thread->user->role_color }}">{{ $thread->user->display_name ?: $thread->user->name }}</span>
                         &bull; {{ $thread->created_at->diffForHumans() }}
                    </div>
                </div>
                <div style="width:80px; text-align:right;">
                    <div style="font-size:12px; font-weight:bold; color:#fff;">{{ $thread->posts_count }}</div>
                    <div style="font-size:10px; color:var(--muted);">replies</div>
                </div>
                <div style="width:80px; text-align:right;">
                    <div style="font-size:12px; font-weight:bold; color:#fff;">{{ $thread->view_count }}</div>
                    <div style="font-size:10px; color:var(--muted);">views</div>
                </div>
            </div>
        @empty
            <div style="padding:32px; text-align:center; color:var(--muted);">
                No threads in this category yet. Be the first to start one!
            </div>
        @endforelse
    </div>

    <div style="margin-top:24px;">
        {{ $threads->links() }}
    </div>
</div>
@endsection
