<?php
namespace App\Services\Payment;

use App\Models\AdmissionNumberSetting;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\NotificationLog;
use App\Models\Payment;
use App\Services\CMS\EmailTemplateService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentService
{
    public function __construct(
        private PaystackService $paystack,
        private InstallmentService $installmentService,
    ) {}

    public function initiate(Enrollment $enrollment, string $gateway = 'paystack', array $options = []): array
    {
        $reference = 'ML-' . strtoupper(Str::random(12));
        $amount    = $options['amount'] ?? ($enrollment->course->effective_price - ($enrollment->discount_amount ?? 0));

        $payment = Payment::create([
            'user_id'       => $enrollment->user_id,
            'enrollment_id' => $enrollment->id,
            'reference'     => $reference,
            'gateway'       => $gateway,
            'amount'        => $amount,
            'currency'      => $enrollment->currency ?? 'NGN',
            'status'        => 'pending',
        ]);

        $callbackUrl = route('enroll.payment.callback', $reference);

        if ($gateway === 'paystack') {
            $data = $this->paystack->initializeTransaction([
                'email'        => $enrollment->user->email,
                'amount'       => $amount,
                'reference'    => $reference,
                'callback_url' => $callbackUrl,
                'metadata'     => [
                    'enrollment_id' => $enrollment->id,
                    'course'        => $enrollment->course->title,
                    'payment_type'  => $options['payment_type'] ?? 'full',
                ],
            ]);

            return [
                'payment'       => $payment,
                'reference'     => $reference,
                'amount'        => $amount,
                'currency'      => $payment->currency,
                'authorization_url' => $data['authorization_url'],
                'access_code'   => $data['access_code'],
            ];
        }

        // Fallback for demo/test: return direct callback
        return [
            'payment'           => $payment,
            'reference'         => $reference,
            'amount'            => $amount,
            'currency'          => $payment->currency,
            'authorization_url' => $callbackUrl,
        ];
    }

    public function verify(string $reference, string $gateway): bool
    {
        $payment = Payment::where('reference', $reference)->firstOrFail();

        if ($payment->status === 'success') {
            return true; // Already verified
        }

        try {
            if ($gateway === 'paystack' && config('services.paystack.secret_key')) {
                $data = $this->paystack->verifyTransaction($reference);
                $payment->update([
                    'status'   => 'success',
                    'paid_at'  => now(),
                    'gateway_response' => $data['gateway_response'] ?? 'Approved',
                    'metadata' => $data,
                ]);
            } else {
                // Test/dev mode
                $payment->update(['status' => 'success', 'paid_at' => now()]);
            }
        } catch (\Exception $e) {
            $payment->update(['status' => 'failed']);
            Log::error("Payment verification failed: {$reference}", ['error' => $e->getMessage()]);
            throw $e;
        }

        $enrollment = $payment->enrollment;
        $enrollment->update([
            'status'         => 'active',
            'payment_status' => 'paid',
            'amount_paid'    => $payment->amount,
            'enrolled_at'    => now(),
        ]);

        $enrollment->course->increment('total_students');
        $this->generateInvoice($payment);
        $this->assignAdmissionNumber($enrollment);
        $this->sendConfirmationNotification($enrollment);

        return true;
    }

    public function handleWebhook(array $payload): void
    {
        $event     = $payload['event'] ?? '';
        $reference = $payload['data']['reference'] ?? null;

        if ($event === 'charge.success' && $reference) {
            try {
                $this->verify($reference, 'paystack');
            } catch (\Exception $e) {
                Log::error("Webhook processing failed for {$reference}", ['error' => $e->getMessage()]);
            }
        }
    }

    protected function generateInvoice(Payment $payment): Invoice
    {
        $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad($payment->id, 5, '0', STR_PAD_LEFT);

        return Invoice::create([
            'user_id'        => $payment->user_id,
            'payment_id'     => $payment->id,
            'invoice_number' => $invoiceNumber,
            'subtotal'       => $payment->amount,
            'discount'       => $payment->enrollment->discount_amount ?? 0,
            'tax'            => 0,
            'total'          => $payment->amount,
            'currency'       => $payment->currency,
            'status'         => 'paid',
            'paid_at'        => now(),
            'line_items'     => [
                ['description' => $payment->enrollment->course->title, 'amount' => $payment->amount],
            ],
        ]);
    }

    protected function assignAdmissionNumber(Enrollment $enrollment): void
    {
        $user = $enrollment->user;
        if ($user->admission_number) return;

        $courseCode = $enrollment->course->category?->name
            ? strtoupper(substr($enrollment->course->category->name, 0, 3))
            : null;

        $settings = AdmissionNumberSetting::instance();
        $number   = $settings->generateNumber($courseCode);
        $user->update(['admission_number' => $number]);
    }

    protected function sendConfirmationNotification(Enrollment $enrollment): void
    {
        NotificationLog::create([
            'user_id' => $enrollment->user_id,
            'title'   => 'Enrollment Confirmed!',
            'message' => "You are now enrolled in {$enrollment->course->title}. Your admission number is {$enrollment->user->admission_number}. Start learning now!",
            'type'    => 'success',
        ]);

        try {
            $emailService = app(EmailTemplateService::class);
            $emailService->send('enrollment_confirmation', $enrollment->user, [
                'student_name'     => $enrollment->user->full_name,
                'course_name'      => $enrollment->course->title,
                'admission_number' => $enrollment->user->admission_number ?? 'Pending',
                'enrolled_at'      => now()->format('M d, Y'),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send enrollment email', ['error' => $e->getMessage()]);
        }
    }
}
