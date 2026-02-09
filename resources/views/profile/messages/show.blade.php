@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 800px; margin: 0 auto; padding: 20px;">
    <div class="card p-4" style="height: 80vh; display:flex; flex-direction:column;">
        <!-- Header -->
        <div style="border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:15px; margin-bottom:15px; display:flex; align-items:center; justify-content:space-between;">
            <div style="display:flex; align-items:center; gap:10px;">
                <a href="{{ route('messages.index') }}" style="text-decoration:none; color:var(--muted); font-size:20px; margin-right:10px;">&larr;</a>
                <h2 style="margin:0; font-size:18px;">{{ $user->name }}</h2>
            </div>
            <a href="{{ route('profile.show', $user->id) }}" style="font-size:12px; color:var(--accent); text-decoration:none;">View Profile</a>
        </div>

        <!-- Messages Area -->
        <div style="flex:1; overflow-y:auto; display:flex; flex-direction:column; gap:10px; padding-right:10px; margin-bottom:20px;" id="message-container">
            @foreach($messages as $message)
                @php
                    $isMe = $message->sender_id == auth()->id();
                @endphp
                <div style="display:flex; flex-direction:column; align-items: {{ $isMe ? 'flex-end' : 'flex-start' }}; margin-bottom:10px;">
                    <div style="font-size:10px; color:var(--muted); margin-bottom:2px; margin-left:4px; margin-right:4px;">
                        {{ $isMe ? 'Du' : $user->name }}
                    </div>
                    <div style="max-width:70%; padding:10px 15px; border-radius:12px; font-size:14px; line-height:1.4; box-shadow:0 2px 4px rgba(0,0,0,0.2);
                        {{ $isMe 
                            ? 'background:var(--accent); color:#fff; border-bottom-right-radius:2px;' 
                            : 'background:rgba(255,255,255,0.1); color:#e6eef6; border-bottom-left-radius:2px;' 
                        }}">
                        {{ $message->body }}
                        <div style="font-size:9px; opacity:0.6; text-align:right; margin-top:4px;">
                            {{ $message->created_at->format('H:i') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Input Area -->
        <form action="{{ route('messages.store') }}" method="POST" style="display:flex; gap:10px; border-top:1px solid rgba(255,255,255,0.1); padding-top:15px;">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $user->id }}">
            <input type="text" name="body" class="input-field" placeholder="Type a message..." required style="flex:1; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff; border-radius:20px; padding:10px 15px;" autofocus>
            <button type="submit" class="btn" style="background:var(--accent); color:#fff; border-radius:20px; padding:0 20px;">Send</button>
        </form>
    </div>
</div>

<script>
    // Scroll to bottom
    const container = document.getElementById('message-container');
    container.scrollTop = container.scrollHeight;
</script>
@endsection
