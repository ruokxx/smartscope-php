@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 800px; margin: 0 auto; padding: 20px;">
    <div class="card p-4">
        <h2 style="margin-top:0; margin-bottom:20px;">{{ __('My Messages') }}</h2>

        @if($conversations->count() > 0)
            <div style="display:flex; flex-direction:column; gap:10px;">
                @foreach($conversations as $userId => $messages)
                    @php
                        $otherUser = \App\Models\User::find($userId);
                        $lastMessage = $messages->first();
                        $unreadCount = $messages->where('receiver_id', auth()->id())->whereNull('read_at')->count();
                    @endphp
                    @if($otherUser)
                        <a href="{{ route('messages.show', $otherUser) }}" style="display:flex; align-items:center; gap:15px; padding:15px; background:rgba(255,255,255,0.05); border-radius:8px; text-decoration:none; color:inherit; transition:background 0.2s; border:1px solid {{ $unreadCount > 0 ? 'var(--accent)' : 'transparent' }};">
                            <div style="width:40px; height:40px; border-radius:50%; background:var(--muted); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:bold;">
                                {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                            </div>
                            <div style="flex:1;">
                                <div style="display:flex; justify-content:space-between;">
                                    <span style="font-weight:600; font-size:16px;">{{ $otherUser->name }}</span>
                                    <span style="font-size:12px; color:var(--muted);">{{ $lastMessage->created_at->diffForHumans() }}</span>
                                </div>
                                <div style="font-size:14px; color:var(--muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:500px;">
                                    @if($lastMessage->sender_id == auth()->id())
                                        <span style="opacity:0.7;">You:</span>
                                    @endif
                                    {{ $lastMessage->body }}
                                </div>
                            </div>
                            @if($unreadCount > 0)
                                <div style="background:var(--accent); color:#fff; padding:2px 8px; border-radius:12px; font-size:12px; font-weight:bold;">
                                    {{ $unreadCount }}
                                </div>
                            @endif
                        </a>
                    @endif
                @endforeach
            </div>
        @else
            <p style="text-align:center; padding:40px; color:var(--muted);">{{ __('No messages yet.') }}</p>
        @endif
    </div>
</div>
@endsection
