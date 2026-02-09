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
        return view('admin.moderation.index', compact('pendingImages'));
    }

    public function approve($id)
    {
        $img = Image::findOrFail($id);
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
