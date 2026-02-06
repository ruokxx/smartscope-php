<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Image;
use App\Models\Obj;
use App\Models\User;
use App\Models\Scope;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function index(Request $req)
    {
        $q = $req->get('q');
        $images = Image::with('user','object','scopeModel')
            ->when($q, fn($b)=>$b->where('filename','like',"%{$q}%"))
            ->orderBy('upload_time','desc')->paginate(25);
        return view('admin.images.index', compact('images','q'));
    }

    public function create()
    {
        // optional: show create form if needed
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
            'image' => 'required|image|max:10240',
            'processing_software'=>'nullable|string|max:255',
            'notes'=>'nullable|string',
        ]);

        $file = $req->file('image');
        $filename = time().'_'.preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $path = $file->storeAs('public/uploads', $filename);

        Image::create([
            'user_id' => $data['user_id'] ?? null,
            'object_id' => $data['object_id'] ?? null,
            'scope_id' => $data['scope_id'] ?? null,
            'filename' => $filename,
            'path' => $path,
            'processing_software' => $data['processing_software'] ?? null,
            'notes' => $data['notes'] ?? null,
            'approved' => 1,
        ]);

        return redirect()->route('admin.images.index')->with('success','Image uploaded');
    }

    public function update(Request $req, Image $image)
    {
        $data = $req->validate([
            'user_id'=>'nullable|exists:users,id',
            'object_id'=>'nullable|exists:objects,id',
            'scope_id'=>'nullable|exists:scopes,id',
            'approved'=>'sometimes|in:0,1',
            'processing_software'=>'nullable|string|max:255',
            'notes'=>'nullable|string',
        ]);
        $data['approved'] = isset($data['approved']) && $data['approved']=='1' ? 1 : 0;
        $image->update($data);
        return redirect()->route('admin.images.index')->with('success','Image updated');
    }

    public function destroy(Image $image)
    {
        if ($image->path && Storage::exists($image->path)) {
            Storage::delete($image->path);
        }
        $image->delete();
        return redirect()->route('admin.images.index')->with('success','Image deleted');
    }

    public function approve(Image $image)
    {
        $image->approved = 1;
        $image->save();
        return redirect()->route('admin.images.index')->with('success','Image approved');
    }
}
