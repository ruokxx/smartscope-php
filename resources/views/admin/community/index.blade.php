@extends('admin.layouts.app')

@section('admin-content')
    <h2>Community Management</h2>

    <div class="panel">
        <style>
            @@media (max-width: 900px) {
                .panel table, .panel thead, .panel tbody, .panel th, .panel td, .panel tr { display: block; }
                .panel thead tr { position: absolute; top: -9999px; left: -9999px; }
                .panel tr { border: 1px solid rgba(255,255,255,0.05); margin-bottom: 16px; border-radius: 8px; background: rgba(255,255,255,0.02); padding: 12px; }
                .panel td { border: none; border-bottom: 1px solid rgba(255,255,255,0.03); position: relative; padding: 8px 0 8px 40%; text-align: right; min-height: 40px; display: flex; align-items: center; justify-content: flex-end; }
                .panel td:before { position: absolute; top: 12px; left: 0; width: 35%; padding-right: 10px; white-space: nowrap; font-weight: bold; color: var(--muted); content: attr(data-label); text-align: left; }
                .panel td:last-child { border-bottom: 0; padding-bottom: 0; justify-content: flex-end; }
            }
        </style>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Content</th>
                    <th>Date</th>
                    <th>Stats</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($posts as $post)
                    <tr>
                        <td data-label="ID">{{ $post->id }}</td>
                        <td data-label="User">
                            <a href="{{ route('profile.show', $post->user->id) }}" style="color:{{ $post->user->role_color }}">
                                {{ $post->user->name }}
                            </a>
                        </td>
                        <td data-label="Content" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:300px;">
                            {{ \Illuminate\Support\Str::limit($post->content, 50) }}
                        </td>
                        <td data-label="Date" style="font-size:12px; color:var(--muted);">{{ $post->created_at->format('Y-m-d H:i') }}</td>
                        <td data-label="Stats">{{ $post->comments->count() }} Comments</td>
                        <td data-label="Actions">
                            <form action="{{ route('admin.community.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Delete this post?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn" style="background:var(--danger); color:#fff; padding:4px 8px; font-size:12px;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="margin-top:12px;">{{ $posts->links() }}</div>
    </div>
@endsection
