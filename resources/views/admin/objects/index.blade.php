@extends('admin.layouts.app')

@section('admin-content')
<div class="card full">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <h1 style="margin:0; font-size:24px;">Deep Sky Objects</h1>
        <a href="{{ route('admin.objects.create') }}" class="btn btn-primary" style="background:var(--accent); color:#fff; border:none; padding:10px 20px; border-radius:6px; text-decoration:none; font-weight:600;">
            + Add New Object
        </a>
    </div>

    <!-- Search -->
    <div class="card" style="padding:16px; margin-bottom:24px; background:rgba(255,255,255,0.02);">
        <form action="{{ route('admin.objects.index') }}" method="GET" style="display:flex; gap:10px;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, catalog, type..." style="flex:1; padding:10px; border-radius:6px; border:1px solid rgba(255,255,255,0.1); background:rgba(0,0,0,0.2); color:#fff;">
            <button type="submit" class="btn" style="background:var(--accent2); color:#fff; padding:0 20px;">Search</button>
            @if(request('search'))
                <a href="{{ route('admin.objects.index') }}" class="btn" style="background:rgba(255,255,255,0.1); color:#fff; padding:10px 16px; text-decoration:none;">Clear</a>
            @endif
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background:rgba(46, 204, 113, 0.2); color:#2ecc71; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid rgba(46, 204, 113, 0.3);">
            {{ session('success') }}
        </div>
    @endif

    <div class="card" style="background:rgba(255,255,255,0.05); border-radius:12px; overflow:hidden;">
        <table style="width:100%; border-collapse:collapse; text-align:left;">
            <thead>
                <tr style="background:rgba(255,255,255,0.05); border-bottom:1px solid rgba(255,255,255,0.1);">
                    <th style="padding:16px; font-weight:600; font-size:14px; color:var(--muted);">Catalog</th>
                    <th style="padding:16px; font-weight:600; font-size:14px; color:var(--muted);">Name</th>
                    <th style="padding:16px; font-weight:600; font-size:14px; color:var(--muted);">Type</th>
                    <th style="padding:16px; font-weight:600; font-size:14px; color:var(--muted);">Coords (RA/Dec)</th>
                    <th style="padding:16px; font-weight:600; font-size:14px; color:var(--muted); text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($objects as $object)
                    <tr style="border-bottom:1px solid rgba(255,255,255,0.05); transition:background 0.2s;">
                        <td style="padding:16px; font-weight:bold; color:var(--accent);">
                            {{ $object->catalog }}
                        </td>
                        <td style="padding:16px; font-weight:500;">
                            {{ $object->name }}
                        </td>
                        <td style="padding:16px; color:var(--muted); font-size:14px;">
                            <span style="background:rgba(255,255,255,0.05); padding:2px 6px; border-radius:4px;">{{ $object->type }}</span>
                        </td>
                        <td style="padding:16px; color:var(--muted); font-size:12px; font-family:monospace;">
                            {{ $object->ra ?? '-' }} / {{ $object->dec ?? '-' }}
                        </td>
                        <td style="padding:16px; text-align:right;">
                            <a href="{{ route('admin.objects.edit', $object->id) }}" class="btn" style="display:inline-block; margin-right:8px; text-decoration:none; color:#fff; background:rgba(255,255,255,0.1); padding:6px 12px; border-radius:4px; font-size:12px;">
                                ✏ Edit
                            </a>
                            <form action="{{ route('admin.objects.destroy', $object->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete {{ $object->name }}? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn" style="background:rgba(231, 76, 60, 0.1); color:#e74c3c; border:none; padding:6px 12px; border-radius:4px; font-size:12px; cursor:pointer;">
                                    🗑
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:40px; text-align:center; color:var(--muted);">
                            No objects found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">
        {{ $objects->appends(['search' => request('search')])->links() }}
    </div>
</div>
@endsection
