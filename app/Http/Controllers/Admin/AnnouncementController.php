<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest()->get();
        return view('admin.announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'           => 'required|in:bar,ticker,popup',
            'title'          => 'nullable|string|max:200',
            'message'        => 'nullable|string|max:1000',
            'body_html'      => 'nullable|string',
            'media_type'     => 'nullable|in:none,image,video,youtube',
            'media_file'     => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,webm,ogg|max:51200',
            'media_youtube'  => 'nullable|url|max:500',
            'url'            => 'nullable|url|max:500',
            'url_text'       => 'nullable|string|max:100',
            'bg_color'       => 'nullable|string|max:20',
            'text_color'     => 'nullable|string|max:20',
            'is_active'      => 'nullable|boolean',
            'is_dismissible' => 'nullable|boolean',
            'starts_at'      => 'nullable|date',
            'ends_at'        => 'nullable|date|after_or_equal:starts_at',
        ]);

        $data['is_active']      = $request->boolean('is_active');
        $data['is_dismissible'] = $request->boolean('is_dismissible');

        // Handle media upload
        if ($request->hasFile('media_file')) {
            $path = $request->file('media_file')->store('announcements', 'public');
            $data['media_url']  = Storage::url($path);
            $data['media_type'] = str_starts_with($request->file('media_file')->getMimeType(), 'video') ? 'video' : 'image';
        } elseif ($request->filled('media_youtube')) {
            $data['media_url']  = $request->input('media_youtube');
            $data['media_type'] = 'youtube';
        } else {
            $data['media_url']  = null;
            $data['media_type'] = $request->input('media_type') === 'none' ? null : ($data['media_type'] ?? null);
        }

        unset($data['media_file'], $data['media_youtube']);

        // For non-popup types, body_html not needed
        if ($data['type'] !== 'popup') {
            $data['body_html'] = null;
            $data['media_url'] = null;
            $data['media_type'] = null;
        }

        // message required for bar/ticker
        if ($data['type'] !== 'popup' && empty($data['message'])) {
            return back()->withErrors(['message' => 'Message is required for ticker/bar types.'])->withInput();
        }

        Announcement::create($data);
        return back()->with('success', 'Announcement created.');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'type'           => 'required|in:bar,ticker,popup',
            'title'          => 'nullable|string|max:200',
            'message'        => 'nullable|string|max:1000',
            'body_html'      => 'nullable|string',
            'media_type'     => 'nullable|in:none,image,video,youtube',
            'media_file'     => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,webm,ogg|max:51200',
            'media_youtube'  => 'nullable|url|max:500',
            'url'            => 'nullable|url|max:500',
            'url_text'       => 'nullable|string|max:100',
            'bg_color'       => 'nullable|string|max:20',
            'text_color'     => 'nullable|string|max:20',
            'is_active'      => 'nullable|boolean',
            'is_dismissible' => 'nullable|boolean',
            'starts_at'      => 'nullable|date',
            'ends_at'        => 'nullable|date|after_or_equal:starts_at',
        ]);

        $data['is_active']      = $request->boolean('is_active');
        $data['is_dismissible'] = $request->boolean('is_dismissible');

        // Handle media upload
        if ($request->hasFile('media_file')) {
            // Delete old file if it was stored locally
            if ($announcement->media_url && !str_contains($announcement->media_url, 'youtube')) {
                $oldPath = str_replace('/storage/', '', $announcement->media_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('media_file')->store('announcements', 'public');
            $data['media_url']  = Storage::url($path);
            $data['media_type'] = str_starts_with($request->file('media_file')->getMimeType(), 'video') ? 'video' : 'image';
        } elseif ($request->filled('media_youtube')) {
            $data['media_url']  = $request->input('media_youtube');
            $data['media_type'] = 'youtube';
        } elseif ($request->input('media_type') === 'none') {
            $data['media_url']  = null;
            $data['media_type'] = null;
        } else {
            // Keep existing
            $data['media_url']  = $announcement->media_url;
            $data['media_type'] = $announcement->media_type;
        }

        unset($data['media_file'], $data['media_youtube']);

        // For non-popup types, clear rich fields
        if ($data['type'] !== 'popup') {
            $data['body_html'] = null;
            $data['media_url'] = null;
            $data['media_type'] = null;
        }

        if ($data['type'] !== 'popup' && empty($data['message'])) {
            return back()->withErrors(['message' => 'Message is required for ticker/bar types.'])->withInput();
        }

        $announcement->update($data);
        return back()->with('success', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement)
    {
        // Clean up uploaded media file
        if ($announcement->media_url && in_array($announcement->media_type, ['image', 'video'])) {
            $path = str_replace('/storage/', '', $announcement->media_url);
            Storage::disk('public')->delete($path);
        }
        $announcement->delete();
        return back()->with('success', 'Announcement deleted.');
    }
}
