@extends('admin.layouts.app')

@section('admin-content')
<div class="card full">
    
    <div style="margin-bottom:24px;">
        <a href="{{ route('admin.objects.index') }}" style="color:var(--muted); text-decoration:none;">&larr; Back to Objects</a>
        <h1 style="margin-top:8px; font-size:24px;">Edit Object: {{ $object->name }}</h1>
    </div>

    <div class="card" style="background:rgba(255,255,255,0.05); padding:24px; border-radius:12px;">
        <form action="{{ route('admin.objects.update', $object->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:20px;">
                <!-- Catalog -->
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:8px;">Catalog Designation <span style="color:#e74c3c">*</span></label>
                    <input type="text" name="catalog" required value="{{ old('catalog', $object->catalog) }}" style="width:100%; padding:10px; border-radius:6px; border:1px solid rgba(255,255,255,0.1); background:rgba(0,0,0,0.2); color:#fff;">
                    @error('catalog') <div style="color:#e74c3c; font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
                </div>

                <!-- Name -->
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:8px;">Common Name <span style="color:#e74c3c">*</span></label>
                    <input type="text" name="name" required value="{{ old('name', $object->name) }}" style="width:100%; padding:10px; border-radius:6px; border:1px solid rgba(255,255,255,0.1); background:rgba(0,0,0,0.2); color:#fff;">
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
                            <option value="{{ $type }}" {{ (old('type', $object->type) == $type) ? 'selected' : '' }}>{{ $type }}</option>
                         @endforeach
                         <option value="Other" {{ (in_array(old('type', $object->type), ['Spiral Galaxy','Elliptical Galaxy','Emission Nebula','Reflection Nebula','Planetary Nebula','Dark Nebula','Supernova Remnant','Open Cluster','Globular Cluster','Star'])) ? '' : 'selected' }}>Other</option>
                    </select>
                </div>

                <!-- Empty -->
                <div></div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:20px;">
                <!-- RA -->
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:8px;">Right Ascension (RA)</label>
                    <input type="text" name="ra" value="{{ old('ra', $object->ra) }}" style="width:100%; padding:10px; border-radius:6px; border:1px solid rgba(255,255,255,0.1); background:rgba(0,0,0,0.2); color:#fff;">
                </div>

                <!-- Dec -->
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:8px;">Declination (Dec)</label>
                    <input type="text" name="dec" value="{{ old('dec', $object->dec) }}" style="width:100%; padding:10px; border-radius:6px; border:1px solid rgba(255,255,255,0.1); background:rgba(0,0,0,0.2); color:#fff;">
                </div>
            </div>

            <!-- Description -->
            <div style="margin-bottom:24px;">
                <label style="display:block; font-weight:600; margin-bottom:8px;">Description</label>
                <textarea name="description" rows="5" style="width:100%; padding:10px; border-radius:6px; border:1px solid rgba(255,255,255,0.1); background:rgba(0,0,0,0.2); color:#fff; font-family:inherit;">{{ old('description', $object->description) }}</textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px;">
                <a href="{{ route('admin.objects.index') }}" class="btn" style="background:rgba(255,255,255,0.1); color:#fff; padding:10px 20px; text-decoration:none; border-radius:6px;">Cancel</a>
                <button type="submit" class="btn" style="background:var(--accent); color:#fff; border:none; padding:10px 24px; border-radius:6px; font-weight:600; cursor:pointer;">Update Object</button>
            </div>

        </form>
    </div>
</div>
@endsection
