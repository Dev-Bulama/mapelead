<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AppSettingsApiController extends Controller
{
    use ApiResponseTrait;

    /** Public: return app-wide settings the mobile app needs at startup */
    public function index(): JsonResponse
    {
        $rows = DB::table('app_settings')->get()->pluck('value', 'key');

        return $this->success([
            'logo_url' => $rows->get('logo_url'),
            'app_name' => $rows->get('app_name', 'MAPELEAD LIMITED'),
            'tagline'  => $rows->get('tagline', 'Learn Today, Build Tomorrow'),
        ]);
    }

    /** Admin: update a setting value */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'key'   => 'required|string|max:100',
            'value' => 'nullable|string|max:2000',
        ]);

        DB::table('app_settings')->updateOrInsert(
            ['key' => $request->key],
            ['value' => $request->value, 'updated_at' => now()]
        );

        return $this->success(['message' => 'Setting updated']);
    }

    /** Admin: upload a new logo and store the URL */
    public function uploadLogo(Request $request): JsonResponse
    {
        $request->validate(['logo' => 'required|image|max:2048']);

        $path = $request->file('logo')->store('logos', 'public');
        $url  = Storage::disk('public')->url($path);

        DB::table('app_settings')->updateOrInsert(
            ['key' => 'logo_url'],
            ['value' => $url, 'updated_at' => now()]
        );

        return $this->success(['logo_url' => $url]);
    }
}
