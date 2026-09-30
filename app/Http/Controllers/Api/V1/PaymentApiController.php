<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Payment;
use App\Services\Payment\PaymentService;
use App\Services\Payment\PaystackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentApiController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly PaystackService $paystackService,
    ) {}

    public function history(Request $request): JsonResponse
    {
        $payments = Payment::where('user_id', $request->user()->id)
            ->with(['enrollment.course:id,title,slug'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return $this->success([
            'payments' => $payments->getCollection()->map(fn($p) => [
                'id'          => $p->id,
                'reference'   => $p->reference,
                'amount'      => (float) ($p->amount ?? 0),
                'currency'    => $p->currency ?? 'NGN',
                'status'      => $p->status,
                'gateway'     => $p->gateway,
                'paid_at'     => $p->paid_at?->toDateTimeString(),
                'created_at'  => $p->created_at?->toDateTimeString(),
                'course'      => $p->enrollment?->course ? [
                    'title' => $p->enrollment->course->title,
                    'slug'  => $p->enrollment->course->slug,
                ] : null,
            ])->values(),
            'meta' => [
                'current_page' => $payments->currentPage(),
                'last_page'    => $payments->lastPage(),
                'total'        => $payments->total(),
            ],
        ]);
    }

    public function webhook(Request $request): \Illuminate\Http\Response
    {
        $payload   = $request->getContent();
        $signature = $request->header('x-paystack-signature');

        if (!$this->paystackService->verifyWebhookSignature($payload, $signature ?? '')) {
            Log::warning('Paystack webhook: invalid signature');
            abort(400);
        }

        $event = json_decode($payload, true);

        if ($event && ($event['event'] ?? '') === 'charge.success') {
            try {
                $this->paymentService->handleWebhook($event);
            } catch (\Throwable $e) {
                Log::error('Paystack webhook processing error', ['error' => $e->getMessage()]);
            }
        }

        return response('OK', 200);
    }
}
