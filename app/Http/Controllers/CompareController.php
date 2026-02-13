<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Obj;
use App\Models\Image;

class CompareController extends Controller
{
    public function show(Request $request, $objectId)
    {
        // Redirect to the new main object view which now includes the comparison functionality
        return redirect()->route('objects.show', ['id' => $objectId, ...$request->all()]);
    }
}
