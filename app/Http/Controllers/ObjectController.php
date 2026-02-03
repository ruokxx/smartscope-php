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

    public function board()
    {
        $user = Auth::user();
        $objects = Obj::orderBy('name')->get();

        // For each object, find user's latest image if exists
        $owned = [];
        if ($user) {
            $imgs = Image::where('user_id', $user->id)->get()->groupBy('object_id');
            foreach ($imgs as $objId => $group) {
                $owned[$objId] = $group->sortByDesc('upload_time')->first();
            }
        }

        return view('board', compact('objects','owned'));
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
        $images = $obj->images()->with('user','scopeModel')->orderBy('upload_time','desc')->get();
        return view('objects.show', compact('obj','images'));
    }

    public function showApi($id)
    {
        $obj = Obj::with('images')->findOrFail($id);
        return response()->json($obj);
    }
}
