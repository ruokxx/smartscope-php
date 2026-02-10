@extends('admin.layouts.app')

@section('admin-content')
<div class="card full">
    
    <div style="margin-bottom:24px;">
        <a href="{{ route('admin.objects.index') }}" style="color:var(--muted); text-decoration:none;">&larr; Back to Objects</a>
        <h1 style="margin-top:8px; font-size:24px;">Add New Deep Sky Object</h1>
    </div>

    <div class="card" style="background:rgba(255,255,255,0.05); padding:24px; border-radius:12px;">
        <form action="{{ route('admin.objects.store') }}" method="POST">
            @csrf

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:20px;">
                <!-- Catalog -->
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:8px;">Catalog Designation <span style="color:#e74c3c">*</span></label>
                    <input type="text" name="catalog" required placeholder="e.g. M31, NGC 7000" style="width:100%; padding:10px; border-radius:6px; border:1px solid rgba(255,255,255,0.1); background:rgba(0,0,0,0.2); color:#fff;">
                    @error('catalog') <div style="color:#e74c3c; font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
                </div>

                <!-- Name -->
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:8px;">Common Name <span style="color:#e74c3c">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Andromeda Galaxy" style="width:100%; padding:10px; border-radius:6px; border:1px solid rgba(255,255,255,0.1); background:rgba(0,0,0,0.2); color:#fff;">
                    @error('name') <div style="color:#e74c3c; font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:20px;">
                <!-- Type -->
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:8px;">Object Type</label>
                    <select name="type" style="width:100%; padding:10px; border-radius:6px; border:1px solid rgba(255,255,255,0.1); background:rgba(0,0,0,0.2); color:#fff;">
                         <option value="">Select Type...</option>
                         @foreach(['Spiral Galaxy','Elliptical Galaxy','Emission Nebula','Reflection Nebula','Planetary Nebula','Dark Nebula','Supernova Remnant','Open Cluster','Globular Cluster','Star'] as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                         @endforeach
                         <option value="Other">Other</option>
                    </select>
                </div>

                <!-- Empty spacer or another field -->
                <div></div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:20px;">
                <!-- RA -->
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:8px;">Right Ascension (RA)</label>
                    <input type="text" name="ra" placeholder="e.g. 00h 42m 44s" style="width:100%; padding:10px; border-radius:6px; border:1px solid rgba(255,255,255,0.1); background:rgba(0,0,0,0.2); color:#fff;">
                </div>

                <!-- Dec -->
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:8px;">Declination (Dec)</label>
                    <input type="text" name="dec" placeholder="e.g. +41° 16′ 09″" style="width:100%; padding:10px; border-radius:6px; border:1px solid rgba(255,255,255,0.1); background:rgba(0,0,0,0.2); color:#fff;">
                </div>
            </div>

            <!-- Description -->
            <div style="margin-bottom:24px;">
                <label style="display:block; font-weight:600; margin-bottom:8px;">Description</label>
                <textarea name="description" rows="5" placeholder="Enter a brief description..." style="width:100%; padding:10px; border-radius:6px; border:1px solid rgba(255,255,255,0.1); background:rgba(0,0,0,0.2); color:#fff; font-family:inherit;"></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px;">
                <a href="{{ route('admin.objects.index') }}" class="btn" style="background:rgba(255,255,255,0.1); color:#fff; padding:10px 20px; text-decoration:none; border-radius:6px;">Cancel</a>
                <button type="submit" class="btn" style="background:var(--accent); color:#fff; border:none; padding:10px 24px; border-radius:6px; font-weight:600; cursor:pointer;">Save Object</button>
            </div>

        </form>
    </div>
</div>
@endsection
