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
        return view('images.create', compact('scopes', 'objects'));
    }

    public function store(Request $req)
    {
        $req->validate([
            'image' => 'required|file|mimes:jpg,jpeg,png|mimetypes:image/jpeg,image/png|max:51200',
            'object_id' => 'nullable|exists:objects,id',
            'bortle' => 'nullable|integer|min:1|max:9',
            'seeing' => 'nullable|string|max:255',
            'session_date' => 'nullable|date',
            'sub_exposure_time' => 'nullable|numeric',
            'gain' => 'nullable|integer',
        ]);

        if (!$req->hasFile('image')) {
            return back()->withErrors(['image' => 'No file uploaded']);
        }

        // Determine storage disk from settings
        $settings = \App\Models\Setting::all()->pluck('value', 'key');
        $disk = $settings['storage_driver'] ?? 'public';

        $user = Auth::user();
        $file = $req->file('image');
        $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads', $filename, $disk);

        $img = Image::create([
            'user_id' => $user ? $user->id : null,
            'object_id' => $req->object_id ?: null,
            'scope_id' => $req->scope_id ?: null,
            'filename' => $filename,
            'path' => $path,
            'disk' => $disk,
            'exposure_total_seconds' => $req->exposure_total_seconds,
            'sub_exposure_seconds' => $req->sub_exposure_seconds,
            'sub_exposure_time' => $req->sub_exposure_time,
            'number_of_subs' => $req->number_of_subs,
            'iso_or_gain' => $req->iso_or_gain,
            'gain' => $req->gain,
            'filter' => $req->filter,
            'bortle' => $req->bortle,
            'seeing' => $req->seeing,
            'session_date' => $req->session_date,
            'processing_software' => $req->processing_software,
            'processing_steps' => $req->processing_steps,
            'notes' => $req->notes,
            'approved' => ($user && ($user->is_admin || $user->is_moderator || !\App\Models\Setting::where('key', 'enable_moderation')->value('value'))) ? true : false,
        ]);

        // Discord Notification
        if ($img->approved) {
            try {
                (new \App\Services\DiscordService())->sendUpload($img);
            }
            catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Discord Upload Notification failed: ' . $e->getMessage());
            }
        }

        $message = $img->approved ? __('Image uploaded') : __('Image uploaded and pending approval.');
        return redirect()->route('objects.show', $req->object_id ?: $img->id)->with('success', $message);
    }

    // API: latest uploads
    public function latest(Request $req)
    {
        $hours = intval($req->get('hours', 2));
        $limit = intval($req->get('limit', 5));
        $since = now()->subHours($hours);
        $images = Image::where('approved', true) // MODERATION
            ->where('upload_time', '>=', $since)
            ->orderBy('upload_time', 'desc')
            ->limit($limit)
            ->get();
        return response()->json($images);
    }
    public function destroy($id)
    {
        $img = Image::findOrFail($id);

        if ($img->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized');
        }

        // Delete file
        if ($img->path) {
            Storage::disk($img->disk ?: 'public')->delete($img->path);
        }

        $img->delete();

        return back()->with('success', 'Image deleted');
    }
}
