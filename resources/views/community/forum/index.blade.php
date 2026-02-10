@extends('layouts.app')

@section('content')
<div class="home-centered-container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <div>
            <a href="{{ route('community.index') }}" style="font-size:12px; color:var(--muted); text-decoration:none;">&larr; Back to Community</a>
            <h2 class="page-title" style="margin:4px 0 0; text-align:left;">Forums</h2>
        </div>
        @if(auth()->user()->is_admin)
            <a href="{{ route('admin.forum.categories.index') }}" class="btn" style="font-size:12px;">Manage Categories</a>
        @endif
    </div>

    @foreach($categories as $category)
        <div class="card" style="margin-bottom:16px; padding:0; overflow:hidden;">
            <div style="background:rgba(255,255,255,0.03); padding:12px 16px; border-bottom:1px solid rgba(255,255,255,0.05); display:flex; justify-content:space-between; align-items:center;">
                <h3 style="margin:0; font-size:16px;">
                    <a href="{{ route('community.forum.category', $category) }}" style="color:inherit; text-decoration:none;">{{ $category->title }}</a>
                </h3>
                @if($category->description)
                    <span style="font-size:12px; color:var(--muted);">{{ $category->description }}</span>
                @endif
            </div>
            
            <!-- Subcategories -->
            @if($category->children->count() > 0)
                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(200px, 1fr)); gap:1px; background:rgba(255,255,255,0.05);">
                    @foreach($category->children as $child)
                        <div style="background:var(--bg-card); padding:12px 16px; display:flex; flex-direction:column;">
                            <a href="{{ route('community.forum.category', $child) }}" style="font-weight:600; color:#e6eef6; text-decoration:none;">{{ $child->title }}</a>
                            @if($child->description)
                                <span style="font-size:11px; color:var(--muted); margin-top:2px;">{{ $child->description }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <div style="padding:0;">
                @forelse($category->threads as $thread)
                    <div style="padding:12px 16px; border-bottom:1px solid rgba(255,255,255,0.02); display:flex; align-items:center; gap:12px;">
                        <div style="flex:1;">
                            <div style="font-size:14px; font-weight:600;">
                                <a href="{{ route('community.forum.thread', $thread) }}" style="color:#e6eef6; text-decoration:none;">{{ $thread->title }}</a>
                                @if($thread->is_pinned) <span style="font-size:10px; background:var(--accent); color:#000; padding:2px 4px; border-radius:4px; margin-left:4px;">PIN</span> @endif
                                @if($thread->is_locked) <span style="font-size:10px; background:#e74c3c; color:#fff; padding:2px 4px; border-radius:4px; margin-left:4px;">LOCKED</span> @endif
                            </div>
                            <div style="font-size:11px; color:var(--muted); margin-top:2px;">
                                by <span style="color:{{ $thread->user->role_color }}">{{ $thread->user->display_name ?: $thread->user->name }}</span> &bull; {{ $thread->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div style="text-align:right; min-width:60px;">
                            <div style="font-size:11px; color:var(--muted);">{{ $thread->posts_count ?? 0 }} replies</div>
                        </div>
                    </div>
                @empty
                    <div style="padding:16px; font-size:13px; color:var(--muted); font-style:italic;">No threads yet.</div>
                @endforelse
                @if($category->threads->count() > 0)
                   <div style="padding:8px 16px; text-align:right;">
                       <a href="{{ route('community.forum.category', $category) }}" style="font-size:12px; color:var(--accent);">View all &rarr;</a>
                   </div>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection
