<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Obj;
use App\Models\Image;

class CompareController extends Controller
{
    public function show(Request $request, $objectId)
    {
        $object = Obj::findOrFail($objectId);

        // Filter Options
        $filters = ['Kein', 'Dualband', 'Astro'];
        // $gains removed (manual input)

        // Base Queries
        $query = Image::where('object_id', $objectId);

        // Apply Filters
        if ($request->filled('min_exposure')) {
            // input is likely in minutes, db is seconds
            $minSeconds = $request->min_exposure * 60;
            $query->where('exposure_total_seconds', '>=', $minSeconds);
        }
        if ($request->filled('filter')) {
            $query->where('filter', $request->filter);
        }
        if ($request->filled('gain')) {
            $query->where('gain', $request->gain);
        }

        // Clone query for each scope to avoid contamination if we were to continue building on $query
        // But here we need to apply scope condition ON TOP of the filtered query

        $dwarf = (clone $query)->whereHas('scopeModel', fn($q) => $q->where('name', 'Dwarf'))
            ->orderBy('exposure_total_seconds', 'desc') // Best exposure first? Or upload_time? User asked for filtering by exposure, maybe sorting by it too make sense? Sticking to upload_time for "latest" matching criteria or maybe exposure is better if comparing quality. Let's stick to upload_time as per original, or maybe let user decide. For now, let's keep upload_time desc as "latest matching".
            ->orderBy('upload_time', 'desc')
            ->first();

        $seestar = (clone $query)->whereHas('scopeModel', fn($q) => $q->where('name', 'Seestar'))
            ->orderBy('exposure_total_seconds', 'desc')
            ->orderBy('upload_time', 'desc')
            ->first();

        return view('compare.show', compact('object', 'dwarf', 'seestar', 'filters'));
    }
}
