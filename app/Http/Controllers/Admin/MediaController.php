<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaLibrary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $media = MediaLibrary::when($request->folder, fn($q, $f) => $q->where('folder', $f))
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%$s%"))
            ->when($request->type, fn($q, $t) => $q->where('mime_type', 'like', "$t/%"))
            ->orderByDesc('created_at')
            ->paginate(24);

        $folders = MediaLibrary::distinct()->whereNotNull('folder')->pluck('folder');
        return view('admin.media.index', compact('media', 'folders'));
    }

    public function upload(Request $request)
    {
        $request->validate(['files.*' => 'required|file|max:10240']);

        $uploaded = [];
        foreach ($request->file('files', []) as $file) {
            $folder   = $request->folder ?? 'uploads/' . date('Y/m');
            $path     = $file->store($folder, 'public');
            $url      = Storage::disk('public')->url($path);

            $media = MediaLibrary::create([
                'user_id'   => auth()->id(),
                'name'      => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'disk'      => 'public',
                'path'      => $path,
                'url'       => $url,
                'size'      => $file->getSize(),
                'folder'    => $folder,
            ]);

            $uploaded[] = ['id' => $media->id, 'url' => $url, 'name' => $media->name];
        }

        return response()->json(['success' => true, 'files' => $uploaded]);
    }

    public function destroy(int $id)
    {
        $media = MediaLibrary::findOrFail($id);
        Storage::disk($media->disk)->delete($media->path);
        $media->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'File deleted!');
    }
}
