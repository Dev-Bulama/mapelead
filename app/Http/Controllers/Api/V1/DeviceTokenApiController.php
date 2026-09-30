<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\DeviceToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceTokenApiController extends Controller
{
    use ApiResponseTrait;

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'token'       => 'required|string|max:500',
            'platform'    => 'required|in:ios,android,web',
            'app_version' => 'nullable|string|max:20',
        ]);

        $user = $request->user();

        DeviceToken::updateOrCreate(
            ['token' => $request->token],
            [
                'user_id'     => $user->id,
                'platform'    => $request->platform,
                'app_version' => $request->app_version,
                'last_used_at'=> now(),
            ]
        );

        // Clean up old tokens for this user (keep last 5 per platform)
        DeviceToken::where('user_id', $user->id)
            ->where('platform', $request->platform)
            ->orderByDesc('last_used_at')
            ->skip(5)
            ->take(PHP_INT_MAX)
            ->delete();

        return $this->success([], message: 'Device token registered');
    }

    public function unregister(Request $request): JsonResponse
    {
        $request->validate(['token' => 'required|string']);

        DeviceToken::where('user_id', $request->user()->id)
            ->where('token', $request->token)
            ->delete();

        return $this->success([], message: 'Device token removed');
    }
}
