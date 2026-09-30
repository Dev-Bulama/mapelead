<?php
namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExpoPushService
{
    private const EXPO_PUSH_URL = 'https://exp.host/--/api/v2/push/send';

    /**
     * Send a push notification to all active device tokens for a user.
     *
     * @param int    $userId
     * @param string $title
     * @param string $body
     * @param array  $data   Arbitrary payload; include 'screen' key for deep linking
     */
    public function sendToUser(int $userId, string $title, string $body, array $data = []): void
    {
        $tokens = DeviceToken::where('user_id', $userId)
            ->where('last_used_at', '>=', now()->subDays(90))
            ->pluck('token')
            ->filter(fn ($t) => str_starts_with($t, 'ExponentPushToken['))
            ->values()
            ->all();

        if (empty($tokens)) {
            return;
        }

        $this->send($tokens, $title, $body, $data);
    }

    /**
     * Send to multiple users at once.
     *
     * @param int[]  $userIds
     */
    public function sendToUsers(array $userIds, string $title, string $body, array $data = []): void
    {
        if (empty($userIds)) return;

        $tokens = DeviceToken::whereIn('user_id', $userIds)
            ->where('last_used_at', '>=', now()->subDays(90))
            ->pluck('token')
            ->filter(fn ($t) => str_starts_with($t, 'ExponentPushToken['))
            ->values()
            ->all();

        if (empty($tokens)) return;

        $this->send($tokens, $title, $body, $data);
    }

    private function send(array $tokens, string $title, string $body, array $data): void
    {
        // Expo accepts up to 100 messages per batch
        $chunks = array_chunk($tokens, 100);

        foreach ($chunks as $chunk) {
            $messages = array_map(fn ($token) => [
                'to'    => $token,
                'title' => $title,
                'body'  => $body,
                'data'  => $data,
                'sound' => 'default',
                'priority' => 'high',
            ], $chunk);

            try {
                $response = Http::withHeaders([
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                ])->post(self::EXPO_PUSH_URL, $messages);

                if (!$response->successful()) {
                    Log::warning('ExpoPushService: non-200 response', [
                        'status' => $response->status(),
                        'body'   => $response->body(),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('ExpoPushService: send failed', ['error' => $e->getMessage()]);
            }
        }
    }
}
