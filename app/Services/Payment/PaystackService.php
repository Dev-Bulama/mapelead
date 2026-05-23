<?php
namespace App\Services\Payment;

use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\NotificationLog;
use App\Models\Payment;
use App\Services\CMS\EmailTemplateService;
use App\Services\LMS\AdmissionNumberService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaystackService
{
    private string $secretKey;
    private string $baseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key', '');
    }

    public function initializeTransaction(array $data): array
    {
        $response = Http::withToken($this->secretKey)
            ->post("{$this->baseUrl}/transaction/initialize", [
                'email'     => $data['email'],
                'amount'    => (int) ($data['amount'] * 100), // kobo
                'reference' => $data['reference'],
                'callback_url' => $data['callback_url'],
                'metadata'  => $data['metadata'] ?? [],
                'channels'  => ['card', 'bank', 'ussd', 'bank_transfer'],
            ]);

        if (!$response->successful() || !$response->json('status')) {
            Log::error('Paystack init failed', ['response' => $response->json()]);
            throw new \Exception('Could not initialize payment: ' . ($response->json('message') ?? 'Unknown error'));
        }

        return $response->json('data');
    }

    public function verifyTransaction(string $reference): array
    {
        $response = Http::withToken($this->secretKey)
            ->get("{$this->baseUrl}/transaction/verify/{$reference}");

        if (!$response->successful()) {
            throw new \Exception('Payment verification failed');
        }

        $data = $response->json('data');

        if ($data['status'] !== 'success') {
            throw new \Exception('Payment was not successful: ' . ($data['gateway_response'] ?? 'Unknown'));
        }

        return $data;
    }

    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $computed = hash_hmac('sha512', $payload, $this->secretKey);
        return hash_equals($computed, $signature);
    }
}
