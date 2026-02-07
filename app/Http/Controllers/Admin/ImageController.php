<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Image;
use App\Models\Obj;
use App\Models\User;
use App\Models\Scope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ImageController extends Controller
{
    public function index(Request $req)
    {
        $q = $req->get('q');
        $images = Image::with('user','object','scopeModel')
            ->when($q, fn($b) => $b->where('filename','like', "%{$q}%"))
            ->orderBy('upload_time','desc')
            ->paginate(25);

        return view('admin.images.index', compact('images','q'));
    }

    public function create()
    {
        $objects = Obj::orderBy('name')->get();
        $scopes = Scope::orderBy('name')->get();
        $users = User::orderBy('id')->get();

        return view('admin.images.create', compact('objects','scopes','users'));
    }

    public function edit(Image $image)
    {
        $objects = Obj::orderBy('name')->get();
        $scopes = Scope::orderBy('name')->get();
        $users = User::orderBy('id')->get();

        return view('admin.images.edit', compact('image','objects','scopes','users'));
    }

public function store(Request $req)
{
    $data = $req->validate([
        'user_id' => 'nullable|exists:users,id',
        'object_id' => 'nullable|exists:objects,id',
        'scope_id' => 'nullable|exists:scopes,id',
        'image' => 'required|file|mimes:jpg,jpeg,png|mimetypes:image/jpeg,image/png|max:51200',
        'exposure_total_seconds' => 'nullable|numeric',
        'number_of_subs' => 'nullable|integer|min:1',
        'processing_software' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
    ]);

    if (! $req->hasFile('image') || ! $req->file('image')->isValid()) {
        return back()->withErrors(['image' => 'No valid file uploaded']);
    }

    $file = $req->file('image');
    $base = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    $safeBase = \Illuminate\Support\Str::slug($base) ?: 'upload';
    $ext = $file->getClientOriginalExtension();
    $filename = time() . '_' . $safeBase . '.' . $ext;

    // store on public disk under uploads/
    $path = $file->storeAs('uploads', $filename, 'public'); // returns uploads/filename

    Image::create([
        'user_id' => $data['user_id'] ?? (auth()->check() ? auth()->id() : null),
        'object_id' => $data['object_id'] ?? null,
        'scope_id' => $data['scope_id'] ?? null,
        'filename' => $filename,
        'path' => $path,
        'exposure_total_seconds' => $data['exposure_total_seconds'] ?? null,
        'number_of_subs' => $data['number_of_subs'] ?? null,
        'processing_software' => $data['processing_software'] ?? null,
        'notes' => $data['notes'] ?? null,
        'approved' => 1,
    ]);

    return redirect()->route('admin.images.index')->with('success','Image uploaded');
}


    public function update(Request $req, Image $image)
    {
        $data = $req->validate([
            'user_id' => 'nullable|exists:users,id',
            'object_id' => 'nullable|exists:objects,id',
            'scope_id' => 'nullable|exists:scopes,id',
            'approved' => 'sometimes|in:0,1',
            'processing_software' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $data['approved'] = isset($data['approved']) && $data['approved'] == '1' ? 1 : 0;
        $image->update($data);

        return redirect()->route('admin.images.index')->with('success', 'Image updated');
    }

    public function destroy(Image $image)
    {
        // delete file from public disk if exists
        if ($image->path && Storage::disk('public')->exists($image->path)) {
            Storage::disk('public')->delete($image->path);
        }

        $image->delete();

        return redirect()->route('admin.images.index')->with('success', 'Image deleted');
    }

    public function approve(Image $image)
    {
        $image->approved = 1;
        $image->save();

        return redirect()->route('admin.images.index')->with('success', 'Image approved');
    }
}
