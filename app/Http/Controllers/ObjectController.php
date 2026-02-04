<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Obj;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;

class ObjectController extends Controller
{
    public function home()
    {
        // latest 5 uploads (last 2 hours) with user relation
        $since = now()->subHours(2);
        $images = Image::with('user')
            ->where('upload_time', '>=', $since)
            ->orderBy('upload_time','desc')
            ->limit(5)
            ->get();

        return view('home', compact('images'));
    }

    public function board(Request $req)
    {
        $user = Auth::user();
        $q = trim($req->get('q', ''));

        // base query for objects with optional search
        $query = Obj::query();
        if ($q !== '') {
            $query->where(function($b) use ($q) {
                $b->where('name', 'like', "%{$q}%")
                  ->orWhere('catalog', 'like', "%{$q}%")
                  ->orWhere('type', 'like', "%{$q}%")
                  ->orWhere('description', 'like', "%{$q}%");
            });
        }

        // paginate results
        $objects = $query->orderBy('name')->paginate(25)->withQueryString();

        // preload user's latest images for visible objects
        $objectIds = $objects->pluck('id')->toArray();
        $owned = [];
        if ($user && count($objectIds)) {
            $imgs = Image::whereIn('object_id', $objectIds)
                ->where('user_id', $user->id)
                ->get()
                ->groupBy('object_id');

            foreach ($imgs as $objId => $group) {
                $owned[$objId] = $group->sortByDesc('upload_time')->first();
            }
        }

        return view('board', compact('objects','owned','q'));
    }

    // API: board with ownership info
    public function boardApi(Request $req)
    {
        $user = $req->user();
        $objects = Obj::orderBy('name')->get();
        $res = $objects->map(function($o) use ($user) {
            $item = $o->toArray();
            $item['owned'] = false;
            $item['image'] = null;
            if ($user) {
                $img = Image::where('object_id',$o->id)->where('user_id',$user->id)->orderBy('upload_time','desc')->first();
                if ($img) { $item['owned'] = true; $item['image'] = $img; }
            }
            return $item;
        });
        return response()->json($res);
    }

    public function show($id)
    {
        $obj = Obj::findOrFail($id);

        // all images for this object, eager loaded
        $allImages = Image::with('user','scopeModel')
            ->where('object_id', $obj->id)
            ->orderBy('upload_time','desc')
            ->get();

        // list distinct uploaders (users)
        $uploaders = $allImages->pluck('user')->unique('id')->values();

        // images grouped by user id
        $imagesByUser = $allImages->groupBy(function($img){ return $img->user ? $img->user->id : 0; });

        // current user's own images for this object
        $myImages = collect();
        if (auth()->check()) {
            $myImages = $imagesByUser->get(auth()->id(), collect());
        }

        return view('objects.show', [
            'obj' => $obj,
            'uploaders' => $uploaders,
            'imagesByUser' => $imagesByUser,
            'myImages' => $myImages,
        ]);
    }

    public function showApi($id)
    {
        $obj = Obj::with('images')->findOrFail($id);
        return response()->json($obj);
    }
}
