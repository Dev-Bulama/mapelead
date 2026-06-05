<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

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
            'message'        => 'required|string|max:1000',
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

        Announcement::create($data);
        return back()->with('success', 'Announcement created.');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'type'           => 'required|in:bar,ticker,popup',
            'title'          => 'nullable|string|max:200',
            'message'        => 'required|string|max:1000',
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

        $announcement->update($data);
        return back()->with('success', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Announcement deleted.');
    }
}
