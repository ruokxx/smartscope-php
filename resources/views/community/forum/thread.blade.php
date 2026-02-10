@extends('layouts.app')

@section('content')
<div class="home-centered-container">
    <div style="margin-bottom:16px;">
        <a href="{{ route('community.forum.category', $thread->category) }}" style="font-size:12px; color:var(--muted); text-decoration:none;">&larr; Back to {{ $thread->category->title }}</a>
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-top:8px;">
            <h2 style="margin:0; font-size:22px;">
                @if($thread->is_pinned) 📌 @endif
                @if($thread->is_locked) 🔒 @endif
                {{ $thread->title }}
            </h2>
            @if(auth()->id() === $thread->user_id || auth()->user()->isModerator())
                <form action="{{ route('community.forum.thread.destroy', $thread) }}" method="POST" onsubmit="return confirm('Delete this thread?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn" style="background:transparent; color:#ef4444; border:1px solid #ef4444; font-size:12px; padding:4px 8px;">Delete Thread</button>
                </form>
            @endif
        </div>
    </div>

    <div style="display:flex; flex-direction:column; gap:16px;">
        @foreach($posts as $post)
            <div class="card" id="post-{{ $post->id }}" style="padding:0; overflow:hidden;">
                <div style="display:flex; border-bottom:1px solid rgba(255,255,255,0.03);">
                    <!-- User Info Column -->
                    <div style="width:140px; padding:16px; background:rgba(0,0,0,0.1); border-right:1px solid rgba(255,255,255,0.05); text-align:center;">
                        <div style="width:64px; height:64px; margin:0 auto 12px; border-radius:50%; overflow:hidden; background:#000;">
                            <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->name }}" style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        <div style="font-weight:bold; font-size:13px; color:{{ $post->user->role_color }}; word-break:break-word;">
                            {{ $post->user->display_name ?: $post->user->name }}
                        </div>
                        @if($post->user->is_admin)
                            <div style="font-size:10px; color:#ff6b6b; margin-top:4px;">ADMIN</div>
                        @elseif($post->user->is_moderator)
                            <div style="font-size:10px; color:#2ecc71; margin-top:4px;">MODERATOR</div>
                        @else
                            <div style="font-size:10px; color:var(--muted); margin-top:4px;">Member</div>
                        @endif
                        <div style="font-size:10px; color:var(--muted); margin-top:8px;">
                            Joined<br>{{ $post->user->created_at->format('M Y') }}
                        </div>
                    </div>

                    <!-- Content Column -->
                    <div style="flex:1; padding:16px; display:flex; flex-direction:column;">
                        <div style="font-size:11px; color:var(--muted); margin-bottom:12px; display:flex; justify-content:space-between;">
                            <span>{{ $post->created_at->format('M d, Y H:i') }}</span>
                            <div style="display:flex; gap:8px;">
                                <a href="#post-{{ $post->id }}" style="color:inherit; text-decoration:none;">#{{ $loop->iteration + (($posts->currentPage()-1) * $posts->perPage()) }}</a>
                                @if((auth()->id() === $post->user_id || auth()->user()->isModerator()) && !$loop->first)
                                    <form action="{{ route('community.forum.post.destroy', $post) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete post?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background:none; border:none; color:#ef4444; cursor:pointer; font-size:11px; padding:0;">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        <div style="font-size:14px; line-height:1.6; color:#e0e6ed; white-space:pre-wrap;">{{ $post->content }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div style="margin-top:24px;">
        {{ $posts->links() }}
    </div>

    @if(!$thread->is_locked)
        <div class="card" style="margin-top:24px;">
            <h3 style="margin-top:0;">Reply</h3>
            <form action="{{ route('community.forum.post.store', $thread) }}" method="POST">
                @csrf
                <textarea name="content" rows="6" required placeholder="Write your reply here..." style="width:100%; margin-bottom:12px;"></textarea>
                <button type="submit" class="btn">Post Reply</button>
            </form>
        </div>
    @else
        <div class="notice" style="margin-top:24px; text-align:center;">
            This thread is locked. You cannot reply.
        </div>
    @endif
</div>
@endsection
