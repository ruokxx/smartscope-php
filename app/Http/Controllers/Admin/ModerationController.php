<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModerationController extends Controller
{
    public function index()
    {
        $pendingImages = Image::where('approved', false)->orderBy('created_at', 'asc')->with('user', 'object')->get();
        $objects = \App\Models\Obj::orderBy('name')->get();
        return view('admin.moderation.index', compact('pendingImages', 'objects'));
    }

    public function approve(Request $req, $id)
    {
        $img = Image::findOrFail($id);

        if ($req->has('object_id') && $req->object_id) {
            $img->object_id = $req->object_id;
        }

        $img->approved = true;
        $img->save();

        return back()->with('success', 'Image approved.');
    }

    public function reject($id)
    {
        $img = Image::findOrFail($id);

        // Delete file
        if ($img->path) {
            Storage::disk('public')->delete($img->path);
        }

        $img->delete();

        return back()->with('success', 'Image rejected and deleted.');
    }
}
