<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('sort_order')->orderBy('title')->get();
        return view('admin.services.index', compact('services'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:200',
            'description' => 'required|string|max:2000',
            'icon'        => 'nullable|string|max:100',
            'image'       => 'nullable|image|max:3072',
            'color'       => 'nullable|string|max:20',
            'link_url'    => 'nullable|url|max:300',
            'link_text'   => 'nullable|string|max:50',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $data['is_active']   = $request->boolean('is_active', true);
        $data['is_featured'] = $request->boolean('is_featured', true);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        Service::create($data);
        return back()->with('success', 'Service added.');
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:200',
            'description' => 'required|string|max:2000',
            'icon'        => 'nullable|string|max:100',
            'image'       => 'nullable|image|max:3072',
            'color'       => 'nullable|string|max:20',
            'link_url'    => 'nullable|url|max:300',
            'link_text'   => 'nullable|string|max:50',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $data['is_active']   = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            if ($service->image) Storage::disk('public')->delete($service->image);
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service->update($data);
        return back()->with('success', 'Service updated.');
    }

    public function destroy(Service $service)
    {
        if ($service->image) Storage::disk('public')->delete($service->image);
        $service->delete();
        return back()->with('success', 'Service deleted.');
    }

    public function create() { return redirect()->route('admin.services.index'); }
    public function show($id) { return redirect()->route('admin.services.index'); }
    public function edit($id) { return redirect()->route('admin.services.index'); }
}
