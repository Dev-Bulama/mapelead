<?php
namespace App\Services\Payment;

use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Str;

class PaymentService
{
    public function initiate(Enrollment $enrollment, string $gateway = 'paystack'): array
    {
        $reference = 'ML-' . strtoupper(Str::random(12));

        $payment = Payment::create([
            'user_id'      => $enrollment->user_id,
            'enrollment_id'=> $enrollment->id,
            'reference'    => $reference,
            'gateway'      => $gateway,
            'amount'       => $enrollment->course->effective_price - $enrollment->discount_amount,
            'currency'     => $enrollment->currency,
            'status'       => 'pending',
        ]);

        return [
            'payment'   => $payment,
            'reference' => $reference,
            'amount'    => $payment->amount,
            'currency'  => $payment->currency,
        ];
    }

    public function verify(string $reference, string $gateway): bool
    {
        $payment = Payment::where('reference', $reference)->firstOrFail();

        // Gateway verification would call Paystack/Flutterwave API here
        // For now we mark as success (real implementation connects to gateway)
        $payment->update([
            'status'  => 'success',
            'paid_at' => now(),
        ]);

        $enrollment = $payment->enrollment;
        $enrollment->update([
            'status'         => 'active',
            'payment_status' => 'paid',
            'amount_paid'    => $payment->amount,
            'enrolled_at'    => now(),
        ]);

        $enrollment->course->increment('total_students');

        $this->generateInvoice($payment);

        return true;
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
}
