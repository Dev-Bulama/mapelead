<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MobileSettingsController extends Controller
{
    public function index()
    {
        try {
            $settings = DB::table('app_settings')->get()->pluck('value', 'key');
        } catch (\Throwable) {
            $settings = collect();
        }

        return view('admin.mobile-settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'nullable|string|max:100',
            'tagline'  => 'nullable|string|max:255',
            'logo'     => 'nullable|image|max:2048',
        ]);

        if ($request->filled('app_name')) {
            DB::table('app_settings')->updateOrInsert(
                ['key' => 'app_name'],
                ['value' => $request->app_name, 'updated_at' => now()]
            );
        }

        if ($request->filled('tagline')) {
            DB::table('app_settings')->updateOrInsert(
                ['key' => 'tagline'],
                ['value' => $request->tagline, 'updated_at' => now()]
            );
        }

        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            // Delete old logo file if stored on disk
            $old = DB::table('app_settings')->where('key', 'logo_url')->value('value');
            if ($old) {
                $relative = str_replace(Storage::disk('public')->url(''), '', $old);
                if (Storage::disk('public')->exists($relative)) {
                    Storage::disk('public')->delete($relative);
                }
            }

            $path = $request->file('logo')->store('logos', 'public');
            $url  = Storage::disk('public')->url($path);

            DB::table('app_settings')->updateOrInsert(
                ['key' => 'logo_url'],
                ['value' => $url, 'updated_at' => now()]
            );
        }

        return back()->with('success', 'Mobile app settings saved successfully.');
    }
}
