<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    public function index()
    {
        $members = TeamMember::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.team.index', compact('members'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'position'     => 'required|string|max:150',
            'department'   => 'nullable|string|max:100',
            'bio'          => 'nullable|string|max:1000',
            'photo'        => 'nullable|image|max:3072',
            'email'        => 'nullable|email|max:150',
            'linkedin_url' => 'nullable|url|max:300',
            'twitter_url'  => 'nullable|url|max:300',
            'github_url'   => 'nullable|url|max:300',
            'sort_order'   => 'nullable|integer|min:0',
            'is_featured'  => 'boolean',
            'is_active'    => 'boolean',
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active']   = $request->boolean('is_active', true);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('team', 'public');
        }

        TeamMember::create($data);
        return back()->with('success', 'Team member added.');
    }

    public function update(Request $request, TeamMember $team)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'position'     => 'required|string|max:150',
            'department'   => 'nullable|string|max:100',
            'bio'          => 'nullable|string|max:1000',
            'photo'        => 'nullable|image|max:3072',
            'email'        => 'nullable|email|max:150',
            'linkedin_url' => 'nullable|url|max:300',
            'twitter_url'  => 'nullable|url|max:300',
            'github_url'   => 'nullable|url|max:300',
            'sort_order'   => 'nullable|integer|min:0',
            'is_featured'  => 'boolean',
            'is_active'    => 'boolean',
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active']   = $request->boolean('is_active');

        if ($request->hasFile('photo')) {
            if ($team->photo) Storage::disk('public')->delete($team->photo);
            $data['photo'] = $request->file('photo')->store('team', 'public');
        }

        $team->update($data);
        return back()->with('success', 'Team member updated.');
    }

    public function destroy(TeamMember $team)
    {
        if ($team->photo) Storage::disk('public')->delete($team->photo);
        $team->delete();
        return back()->with('success', 'Team member deleted.');
    }

    public function create() { return redirect()->route('admin.team.index'); }
    public function show($id) { return redirect()->route('admin.team.index'); }
    public function edit($id) { return redirect()->route('admin.team.index'); }
}
