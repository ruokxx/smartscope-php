@extends('admin.layouts.app')

@section('admin-content')
<div>
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <h2 class="page-title" style="margin:0; text-align:left;">Manage Forum Categories</h2>
        <a href="{{ route('admin.users.index') }}" class="btn" style="background:transparent; border:1px solid rgba(255,255,255,0.1); color:var(--muted);">Back to Admin</a>
    </div>

    <!-- Create Form -->
    <div class="card" style="margin-bottom:24px;">
        <h3>Create Category</h3>
        <form action="{{ route('admin.forum.categories.store') }}" method="POST" style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap;">
            @csrf
            <div style="flex:1; min-width:200px;">
                <label style="display:block; font-size:12px; color:var(--muted); margin-bottom:4px;">Title</label>
                <input type="text" name="title" required placeholder="e.g. General Discussion">
            </div>
            <div style="width:100px;">
                <label style="display:block; font-size:12px; color:var(--muted); margin-bottom:4px;">Order</label>
                <input type="number" name="order" value="0">
            </div>
            <div style="flex:2; min-width:200px;">
                <label style="display:block; font-size:12px; color:var(--muted); margin-bottom:4px;">Parent (Optional)</label>
                <select name="parent_id" style="width:100%;">
                    <option value="">No Parent</option>
                    @foreach($parents as $p)
                        <option value="{{ $p->id }}">{{ $p->title }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:2; min-width:200px;">
                <label style="display:block; font-size:12px; color:var(--muted); margin-bottom:4px;">Description</label>
                <input type="text" name="description" placeholder="Optional description">
            </div>
            <button type="submit" class="btn">Create</button>
        </form>
    </div>

    <!-- List -->
    <div class="card">
        <h3>Existing Categories</h3>
        @if($categories->count() > 0)
            <table style="width:100%; border-collapse:collapse; margin-top:12px;">
                <thead>
                    <tr style="border-bottom:1px solid rgba(255,255,255,0.1); text-align:left;">
                        <th style="padding:8px; color:var(--muted); font-size:12px;">Order</th>
                        <th style="padding:8px; color:var(--muted); font-size:12px;">Title</th>
                        <th style="padding:8px; color:var(--muted); font-size:12px;">Parent</th>
                        <th style="padding:8px; color:var(--muted); font-size:12px;">Description</th>
                        <th style="padding:8px; color:var(--muted); font-size:12px; text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                            <form action="{{ route('admin.forum.categories.update', $category) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <td style="padding:8px;">
                                    <input type="number" name="order" value="{{ $category->order }}" style="width:50px; padding:4px; font-size:12px;">
                                </td>
                                <td style="padding:8px;">
                                    <input type="text" name="title" value="{{ $category->title }}" style="width:100%; padding:4px; font-size:12px;">
                                </td>
                                <td style="padding:8px;">
                                    <select name="parent_id" style="width:100%; padding:4px; font-size:12px;">
                                        <option value="">No Parent</option>
                                        @foreach($parents as $p)
                                            @if($p->id !== $category->id)
                                                <option value="{{ $p->id }}" {{ $category->parent_id == $p->id ? 'selected' : '' }}>{{ $p->title }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </td>
                                <td style="padding:8px;">
                                    <input type="text" name="description" value="{{ $category->description }}" style="width:100%; padding:4px; font-size:12px;">
                                </td>
                                <td style="padding:8px; text-align:right;">
                                    <button type="submit" class="btn" style="padding:4px 8px; font-size:11px;">Save</button>
                                </td>
                            </form>
                            <td style="width:50px; text-align:right; vertical-align:middle;">
                                <form action="{{ route('admin.forum.categories.destroy', $category) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this category?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:transparent; color:#ef4444; border:none; padding:4px; cursor:pointer;">✕</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="muted">No categories yet.</p>
        @endif
    </div>
</div>
@endsection
