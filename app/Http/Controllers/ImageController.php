<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use App\Models\Obj;
use App\Models\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function create()
    {
        $scopes = Scope::all();
        $objects = Obj::orderBy('name')->get();
        return view('images.create', compact('scopes','objects'));
    }

    public function store(Request $req)
    {
        $req->validate([
            'image' => 'required|image|max:10240',
            'object_id' => 'nullable|exists:objects,id',
            'scope_id' => 'nullable|exists:scopes,id',
        ]);

        $user = Auth::user();
        $file = $req->file('image');
        $filename = time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('public/uploads', $filename);

        $img = Image::create([
            'user_id' => $user->id,
            'object_id' => $req->object_id ?: null,
            'scope_id' => $req->scope_id ?: null,
            'filename' => $filename,
            'path' => $path,
            'exposure_total_seconds' => $req->exposure_total_seconds,
            'sub_exposure_seconds' => $req->sub_exposure_seconds,
            'number_of_subs' => $req->number_of_subs,
            'iso_or_gain' => $req->iso_or_gain,
            'filter' => $req->filter,
            'processing_software' => $req->processing_software,
            'processing_steps' => $req->processing_steps,
            'notes' => $req->notes,
        ]);

        return redirect()->route('objects.show', $req->object_id ?: $img->id)->with('success','Image uploaded');
    }

    // API: latest uploads
    public function latest(Request $req)
    {
        $hours = intval($req->get('hours', 2));
        $limit = intval($req->get('limit', 5));
        $since = now()->subHours($hours);
        $images = Image::where('upload_time','>=',$since)->orderBy('upload_time','desc')->limit($limit)->get();
        return response()->json($images);
    }
}
