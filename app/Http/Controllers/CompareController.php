<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Obj;
use App\Models\Image;

class CompareController extends Controller
{
    public function show($objectId)
    {
        $object = Obj::findOrFail($objectId);
        $dwarf = Image::where('object_id',$objectId)->whereHas('scopeModel', fn($q)=>$q->where('name','Dwarf'))->orderBy('upload_time','desc')->first();
        $seestar = Image::where('object_id',$objectId)->whereHas('scopeModel', fn($q)=>$q->where('name','Seestar'))->orderBy('upload_time','desc')->first();
        return view('compare.show', compact('object','dwarf','seestar'));
    }
}
