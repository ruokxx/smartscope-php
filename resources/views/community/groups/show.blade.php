@extends('layouts.app')

@section('content')
<div class="home-centered-container">
    <div style="margin-bottom:24px;">
        <a href="{{ route('community.index') }}" style="color:var(--muted); text-decoration:none;">&larr; Back to Community</a>
    </div>

    <!-- Group Header -->
    <div class="card" style="margin-bottom:24px; padding:32px; text-align:center; position:relative; overflow:hidden;">
        <!-- Background Decoration -->
        <div style="position:absolute; top:0; left:0; right:0; height:100%; background:linear-gradient(135deg, rgba(111,184,255,0.05) 0%, rgba(0,0,0,0) 100%); pointer-events:none;"></div>
        
        <div style="font-size:48px; margin-bottom:16px;">👥</div>
        <h1 style="margin:0 0 8px; font-size:32px;">{{ $group->name }}</h1>
        <p style="color:var(--muted); margin:0 0 24px; max-width:600px; margin-left:auto; margin-right:auto;">{{ $group->description }}</p>
        
        <div style="display:flex; justify-content:center; gap:12px;">
            @if($isOwner)
                <span class="badge" style="background:#f1c40f; color:#000;">👑 Owner</span>
            @elseif($isMember)
                <span class="badge" style="background:var(--accent); color:#fff;">✅ Member</span>
                <form action="{{ route('community.groups.leave', $group) }}" method="POST" onsubmit="return confirm('Leave this group?');">
                    @csrf
                    <button type="submit" class="btn" style="background:rgba(255,255,255,0.1); color:#fff; border:none;">Leave</button>
                </form>
            @elseif($isPending)
                <span class="badge" style="background:rgba(255,255,255,0.1); color:var(--muted);">⏳ Request Pending</span>
            @else
                <form action="{{ route('community.groups.join', $group) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="background:var(--accent); color:#fff; border:none; padding:10px 24px;">Join Group</button>
                </form>
            @endif
        </div>
    </div>

    <!-- Owner Actions: Pending Requests -->
    @if($isOwner && $pendingMembers->count() > 0)
    <div class="card" style="margin-bottom:24px; border:1px solid var(--accent);">
        <h3 style="margin-top:0; color:var(--accent);">Pending Join Requests ({{ $pendingMembers->count() }})</h3>
        <div style="display:flex; flex-direction:column; gap:12px;">
            @foreach($pendingMembers as $user)
                <div style="display:flex; justify-content:space-between; align-items:center; background:rgba(255,255,255,0.05); padding:12px; border-radius:8px;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <img src="{{ $user->avatar_url }}" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
                        <div>
                            <div style="font-weight:bold;">{{ $user->name }}</div>
                            <div style="font-size:12px; color:var(--muted);">Requested {{ $user->pivot->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    <div style="display:flex; gap:8px;">
                        <form action="{{ route('community.groups.approve', [$group, $user]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn" style="background:#2ecc71; color:#fff; border:none; padding:6px 12px; font-size:12px;">Approve</button>
                        </form>
                        <form action="{{ route('community.groups.reject', [$group, $user]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn" style="background:#e74c3c; color:#fff; border:none; padding:6px 12px; font-size:12px;">Reject</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Member List -->
    <div class="card" style="margin-bottom:24px;">
        <h3>Members ({{ $group->members->count() }})</h3>
        <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap:12px;">
            @foreach($group->members as $member)
                <div style="display:flex; justify-content:space-between; align-items:center; background:rgba(255,255,255,0.05); padding:10px; border-radius:8px;">
                    <a href="{{ route('profile.show', $member->id) }}" style="display:flex; align-items:center; gap:10px; text-decoration:none; color:inherit;" class="{{ $member->is_admin ? 'user-admin' : ($member->is_moderator ? 'user-moderator' : '') }}">
                        <img src="{{ $member->avatar_url }}" style="width:32px; height:32px; border-radius:50%; object-fit:cover;">
                        <span style="font-size:14px; font-weight:bold; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $member->name }}</span>
                    </a>
                    
                    @if($group->owner_id === $member->id)
                        <span title="Owner" style="font-size:16px;">👑</span>
                    @elseif($isOwner)
                        <form action="{{ route('community.groups.remove', [$group, $member]) }}" method="POST" onsubmit="return confirm('Remove this member?');">
                            @csrf
                            <button type="submit" title="Remove Member" style="background:transparent; border:none; color:#e74c3c; cursor:pointer; font-size:16px; padding:4px;">
                                ✕
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Group Posts -->
    <div class="card">
        <h3>Group Posts</h3>
        @if($isMember || $isOwner)
            <!-- Post Form -->
            <form action="{{ route('community.posts.store') }}" method="POST" enctype="multipart/form-data" style="margin-bottom:24px;">
                @csrf
                <input type="hidden" name="group_id" value="{{ $group->id }}">
                <textarea name="content" rows="3" placeholder="Post something to this group..." style="width:100%; padding:12px; border-radius:8px; border:1px solid rgba(255,255,255,0.1); background:rgba(0,0,0,0.2); color:#fff; resize:vertical;"></textarea>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
                    <input type="file" name="image" style="font-size:12px; color:var(--muted);">
                    <button type="submit" class="btn btn-primary" style="background:var(--accent); color:#fff; border:none; padding:8px 20px; border-radius:6px; font-weight:600;">Post</button>
                </div>
            </form>

            @forelse($group->posts as $post)
                @include('community.partials.post_card', ['post' => $post])
            @empty
                <p style="color:var(--muted); text-align:center; padding:20px;">No posts yet. Be the first!</p>
            @endforelse
        @else
            <p style="text-align:center; padding:40px; color:var(--muted);">You must be a member to view posts.</p>
        @endif
    </div>
</div>
@endsection
