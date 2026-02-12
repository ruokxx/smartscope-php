<div class="card" style="margin-bottom: 24px; padding:0; overflow:hidden;">
    <!-- Post Header -->
    <div style="padding: 16px; display:flex; gap:12px; align-items:flex-start; border-bottom:1px solid rgba(255,255,255,0.05);">
        <div style="width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg, var(--accent), var(--accent2)); display:flex; align-items:center; justify-content:center; font-weight:bold; color:#fff; font-size:18px;">
            {{ strtoupper(substr($post->user->name, 0, 1)) }}
        </div>
        <div style="flex:1;">
            <div>
                <a href="{{ route('profile.show', $post->user->id) }}" style="color:{{ $post->user->role_color }}; text-decoration:none;" class="{{ $post->user->is_admin ? 'user-admin' : ($post->user->is_moderator ? 'user-moderator' : '') }}">
                    {{ $post->user->display_name ?: $post->user->name }}
                </a>
                @if($post->group)
                    <span style="color:var(--muted);"> ► </span> <a href="{{ route('groups.show', $post->group) }}" style="color:#e6eef6; font-weight:bold; text-decoration:none;">{{ $post->group->name }}</a>
                @endif
                @if($post->user->is_admin) <span style="font-size:10px; background:var(--danger); color:#fff; padding:2px 4px; border-radius:4px; margin-left:4px;">ADMIN</span> @endif
                @if($post->user->is_moderator) <span style="font-size:10px; background:var(--success); color:#fff; padding:2px 4px; border-radius:4px; margin-left:4px;">MOD</span> @endif
            </div>
            <div style="font-size:12px; color:var(--muted);">{{ $post->created_at->diffForHumans() }}</div>
        </div>
        
        @if(auth()->id() === $post->user_id || auth()->user()->isModerator())
            <form action="{{ route('community.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Delete this post?');">
                @csrf
                @method('DELETE')
                <button style="background:transparent; border:none; color:var(--muted); cursor:pointer; font-size:18px;">&times;</button>
            </form>
        @endif
    </div>

    <!-- Post Content -->
    <div style="padding: 16px 16px 8px 16px; font-size:15px; line-height:1.5; color:#e6eef6; white-space:pre-wrap;">
        @if($post->image_path)
            <div style="margin-bottom:12px;">
                <a href="{{ asset('storage/' . $post->image_path) }}" target="_blank">
                    <img src="{{ asset('storage/' . $post->image_path) }}" alt="Post Image" style="max-height: 200px; max-width: 100%; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
                </a>
            </div>
        @endif
        {{ $post->content }}
    </div>

    <!-- Comments Section -->
    <div style="background:rgba(0,0,0,0.2); padding: 16px;">
        @if($post->comments->count() > 0)
            <div style="margin-bottom:16px; display:flex; flex-direction:column; gap:12px;">
                @foreach($post->comments as $comment)
                    <div style="display:flex; gap:10px;">
                        <div style="width:24px; height:24px; border-radius:50%; background:rgba(255,255,255,0.1); display:flex; align-items:center; justify-content:center; font-size:10px; color:#fff;">
                            {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                        </div>
                        <div style="flex:1; background:rgba(255,255,255,0.05); padding:8px 12px; border-radius:8px;">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                <a href="{{ route('profile.show', $comment->user->id) }}" style="font-size:12px; color:{{ $comment->user->role_color }}; text-decoration:none;" class="{{ $comment->user->is_admin ? 'user-admin' : ($comment->user->is_moderator ? 'user-moderator' : '') }}">
                                    {{ $comment->user->display_name ?: $comment->user->name }}
                                </a>
                                <div style="display:flex; gap:8px; align-items:center;">
                                    <span style="font-size:10px; color:var(--muted);">{{ $comment->created_at->diffForHumans() }}</span>
                                    @if(auth()->id() === $comment->user_id || auth()->user()->isModerator())
                                        <form action="{{ route('community.comments.destroy', $comment->id) }}" method="POST" style="line-height:1;">
                                            @csrf @method('DELETE')
                                            <button style="background:transparent; border:none; color:var(--muted); cursor:pointer; font-size:14px; padding:0;">&times;</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            <div style="font-size:13px; margin-top:4px; color:#ddd;">{{ $comment->content }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('community.comments.store', $post->id) }}" method="POST" style="display:flex; gap:8px;">
            @csrf
            <input type="text" name="content" placeholder="Write a comment..." style="flex:1; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:20px; padding:8px 16px; font-size:13px;">
            <button type="submit" style="background:transparent; border:none; color:var(--accent); font-weight:bold; cursor:pointer; font-size:13px;">Send</button>
        </form>
    </div>
</div>
