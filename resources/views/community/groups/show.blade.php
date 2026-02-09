@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 900px; margin: 0 auto; padding: 20px;">
    
    <!-- Group Header -->
    <div class="card" style="margin-bottom:24px; padding:24px; background: linear-gradient(to right, rgba(111,184,255,0.1), transparent);">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <h1 style="margin:0; font-size:28px;">{{ $group->name }}</h1>
                <p style="color:var(--muted); margin-top:8px;">{{ $group->description }}</p>
                <div style="font-size:13px; color:var(--muted); margin-top:12px;">
                    {{ $group->members()->count() }} Members • Created by {{ $group->owner->name }}
                </div>
            </div>
            <div>
                @if($isMember)
                    <form action="{{ route('groups.leave', $group) }}" method="POST" onsubmit="return confirm('Leave this group?');">
                        @csrf
                        <button class="btn" style="background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.2);">Leave Group</button>
                    </form>
                @else
                    <form action="{{ route('groups.join', $group) }}" method="POST">
                        @csrf
                        <button class="btn">Join Group</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Feed -->
    @if($isMember)
        <!-- Member Requests (Owner Only) -->
        @if($isOwner && $pendingMembers->count() > 0)
            <div class="card" style="margin-bottom: 24px; border: 1px solid var(--accent);">
                <h3 style="margin-top:0; font-size:16px; margin-bottom:12px;">{{ __('Member Requests') }}</h3>
                <div style="display:flex; flex-direction:column; gap:12px;">
                    @foreach($pendingMembers as $user)
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:32px; height:32px; border-radius:50%; background:var(--muted); display:flex; align-items:center; justify-content:center; color:#fff; font-size:12px;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span>{{ $user->name }}</span>
                            </div>
                            <div style="display:flex; gap:8px;">
                                <form action="{{ route('groups.approve', [$group, $user]) }}" method="POST">
                                    @csrf
                                    <button class="btn" style="padding:4px 12px; font-size:12px; background:var(--success);">{{ __('Approve') }}</button>
                                </form>
                                <form action="{{ route('groups.remove', [$group, $user]) }}" method="POST" onsubmit="return confirm('Reject user?');">
                                    @csrf
                                    <button class="btn" style="padding:4px 12px; font-size:12px; background:var(--danger);">{{ __('Reject') }}</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Create Post -->
        <div class="card" style="margin-bottom: 24px;">
            <h2 style="margin-top:0; font-size:18px; margin-bottom:16px;">{{ __('Post to') }} {{ $group->name }}</h2>
            <form action="{{ route('community.posts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="group_id" value="{{ $group->id }}">
                <textarea name="content" rows="3" class="input-field" placeholder="{{ __('Share something with the group...') }}" style="width:100%; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:8px; padding:12px; font-family:inherit; resize:vertical;"></textarea>
                
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:12px;">
                    <div>
                        <label for="group-image-upload" style="cursor:pointer; color:var(--accent); font-size:14px; display:flex; align-items:center; gap:6px;">
                            📷 {{ __('Add Image') }}
                        </label>
                        <input type="file" name="image" id="group-image-upload" style="display:none;" accept="image/*" onchange="document.getElementById('group-file-name').innerText = this.files[0].name">
                        <span id="group-file-name" style="font-size:12px; color:var(--muted); margin-left:8px;"></span>
                    </div>
                    <button type="submit" class="btn" style="background:var(--accent); color:#fff; padding:8px 24px;">{{ __('Post') }}</button>
                </div>
            </form>
        </div>

        <!-- Group Posts -->
        @foreach($posts as $post)
            @include('community.partials.post', ['post' => $post])
        @endforeach

        <div style="margin-top:24px;">{{ $posts->links() }}</div>

        <!-- Members List (Owner Only - Simplified) -->
        @if($isOwner)
             <div class="card" style="margin-top: 40px;">
                <h3 style="margin-top:0; font-size:16px;">{{ __('Members Management') }}</h3>
                <p style="font-size:13px; color:var(--muted);">{{ __('Total Members') }}: {{ $group->members()->count() }}</p>
                <!-- Could add a link to a full members management page if list is too long -->
            </div>
        @endif

    @elseif($isPending)
        <div class="card" style="text-align:center; padding:40px;">
            <h3>{{ __('Membership Pending') }}</h3>
            <p style="color:var(--muted);">{{ __('Your request to join this group is waiting for approval by the owner.') }}</p>
        </div>
    @else
        <div class="card" style="text-align:center; padding:40px;">
            <h3>{{ __('Join this group to see posts and participate!') }}</h3>
        </div>
    @endif
</div>
@endsection
