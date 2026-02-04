<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Image;
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

    public function edit(Image $image)
    {
        return view('admin.images.edit', compact('image'));
    }

    public function update(Request $req, Image $image)
    {
        $data = $req->validate([
            'approved'=>'sometimes|boolean',
            'processing_software'=>'nullable|string|max:255',
            'notes'=>'nullable|string',
        ]);
        $image->update($data);
        return redirect()->route('admin.images.index')->with('success','Image updated');
    }

    public function destroy(Image $image)
    {
        // delete file + db record
        if ($image->path) Storage::delete($image->path);
        $image->delete();
        return redirect()->route('admin.images.index')->with('success','Image deleted');
    }

    public function approve(Image $image)
    {
        $image->approved = true;
        $image->save();
        return redirect()->route('admin.images.index')->with('success','Image approved');
    }
}
