@extends('admin.layouts.app')

@section('admin-content')
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <h2>Group Management</h2>
    </div>

    <div class="panel">
        <style>
            @media (max-width: 900px) {
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
                    <th>Name</th>
                    <th>Owner</th>
                    <th>Members</th>
                    <th>Posts</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groups as $group)
                    <tr>
                        <td data-label="ID">{{ $group->id }}</td>
                        <td data-label="Name">
                            <span style="font-weight:bold; color:#fff;">{{ $group->name }}</span>
                            @if($group->description)
                                <div style="font-size:11px; color:var(--muted); max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    {{ $group->description }}
                                </div>
                            @endif
                        </td>
                        <td data-label="Owner">
                            @if($group->owner)
                                <a href="{{ route('profile.show', $group->owner->id) }}" style="color:var(--accent); text-decoration:none;">
                                    {{ $group->owner->name }}
                                </a>
                            @else
                                <span style="color:var(--muted); font-style:italic;">Unknown</span>
                            @endif
                        </td>
                        <td data-label="Members">{{ $group->members_count }}</td>
                        <td data-label="Posts">{{ $group->posts_count }}</td>
                        <td data-label="Created At" style="font-size:12px; color:var(--muted);">{{ $group->created_at->format('Y-m-d') }}</td>
                        <td data-label="Actions">
                            <form action="{{ route('admin.groups.destroy', $group->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this group? All posts and memberships will be removed.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn" style="background:var(--danger, #ef4444); color:#fff; padding:6px 10px; font-size:12px;">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="margin-top:12px;">{{ $groups->links() }}</div>
    </div>
@endsection
