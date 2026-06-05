<?php
namespace App\Services\Payment;

use App\Models\Enrollment;
use App\Models\InstallmentPlan;
use App\Models\InstallmentSchedule;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\DB;

class InstallmentService
{
    public function createPlan(Enrollment $enrollment, array $data): InstallmentPlan
    {
        return DB::transaction(function () use ($enrollment, $data) {
            $totalAmount     = (float) $data['total_amount'];
            $downPayment     = (float) $data['down_payment'];
            $installments    = (int) $data['installment_count'];
            $remaining       = $totalAmount - $downPayment;
            $installmentAmt  = round($remaining / $installments, 2);

            // Remove any unpaid/pending plan so the user can re-submit with different terms
            $existing = InstallmentPlan::where('enrollment_id', $enrollment->id)
                ->whereIn('status', ['active', 'pending'])
                ->first();
            if ($existing) {
                InstallmentSchedule::where('plan_id', $existing->id)->delete();
                $existing->delete();
            }

            $plan = InstallmentPlan::create([
                'enrollment_id'       => $enrollment->id,
                'user_id'             => $enrollment->user_id,
                'course_id'           => $enrollment->course_id,
                'total_amount'        => $totalAmount,
                'down_payment'        => $downPayment,
                'amount_paid'         => $downPayment,
                'outstanding_balance' => $remaining,
                'installment_count'   => $installments,
                'grace_period_days'   => $data['grace_period_days'] ?? 3,
                'auto_lock_on_overdue'=> true,
                'status'              => 'active',
            ]);

            $dueDate = \Carbon\Carbon::parse($data['first_due_date']);

            for ($i = 1; $i <= $installments; $i++) {
                InstallmentSchedule::create([
                    'plan_id'            => $plan->id,
                    'installment_number' => $i,
                    'amount'             => $installmentAmt,
                    'due_date'           => $dueDate->copy()->toDateString(),
                    'status'             => 'pending',
                ]);
                $dueDate->addMonth();
            }

            $enrollment->update([
                'payment_type'   => 'installment',
                'payment_status' => 'partial',
            ]);

            return $plan;
        });
    }

    public function recordPayment(InstallmentPlan $plan, float $amount): void
    {
        DB::transaction(function () use ($plan, $amount) {
            $remaining = $amount;

            $schedules = InstallmentSchedule::where('plan_id', $plan->id)
                ->whereIn('status', ['pending', 'overdue'])
                ->orderBy('due_date')
                ->get();

            foreach ($schedules as $schedule) {
                if ($remaining <= 0) break;

                $owed = $schedule->amount - ($schedule->amount_paid ?? 0);

                if ($remaining >= $owed) {
                    $remaining -= $owed;
                    $schedule->update([
                        'status'     => 'paid',
                        'paid_at'    => now(),
                        'amount_paid'=> $schedule->amount,
                    ]);
                } else {
                    $schedule->update([
                        'status'     => 'partially_paid',
                        'amount_paid'=> ($schedule->amount_paid ?? 0) + $remaining,
                    ]);
                    $remaining = 0;
                }
            }

            $plan->increment('amount_paid', $amount);
            $newBalance = max(0, $plan->outstanding_balance - $amount);
            $plan->update(['outstanding_balance' => $newBalance]);

            $totalPaid = $plan->fresh()->amount_paid;

            if ($newBalance <= 0) {
                $plan->update(['status' => 'completed', 'outstanding_balance' => 0]);
                $plan->enrollment->update([
                    'payment_status'       => 'paid',
                    'amount_paid'          => $plan->total_amount,
                    'access_locked'        => false,
                    'access_locked_reason' => null,
                ]);
            } else {
                $plan->enrollment->update([
                    'payment_status'       => 'partial',
                    'amount_paid'          => $totalPaid,
                    'access_locked'        => false,
                    'access_locked_reason' => null,
                ]);
            }
        });
    }

    public function processOverdue(): int
    {
        $locked = 0;

        $overduePlans = InstallmentPlan::where('status', 'active')
            ->where('outstanding_balance', '>', 0)
            ->where('auto_lock_on_overdue', true)
            ->whereHas('schedule', fn($q) => $q->whereIn('status', ['pending', 'overdue'])->where('due_date', '<', now()))
            ->with(['enrollment', 'enrollment.user'])
            ->get();

        foreach ($overduePlans as $plan) {
            $enrollment = $plan->enrollment;
            if (!$enrollment) continue;

            InstallmentSchedule::where('plan_id', $plan->id)
                ->where('status', 'pending')
                ->where('due_date', '<', now())
                ->update(['status' => 'overdue']);

            $plan->update(['status' => 'overdue']);

            $enrollment->update([
                'access_locked'        => true,
                'access_locked_at'     => now(),
                'access_locked_reason' => 'Payment overdue. Complete your installment payment to regain access.',
                'payment_status'       => 'partial',
            ]);

            NotificationLog::create([
                'user_id' => $enrollment->user_id,
                'title'   => 'Course Access Suspended',
                'message' => 'Your course access has been suspended due to an overdue payment. Please make a payment to restore access.',
                'type'    => 'danger',
            ]);

            $locked++;
        }

        return $locked;
    }

    public function unlockAccess(Enrollment $enrollment, string $reason = 'Manually unlocked by admin'): void
    {
        $enrollment->update([
            'access_locked'        => false,
            'access_locked_reason' => null,
            'access_locked_at'     => null,
        ]);

        if ($enrollment->installmentPlan) {
            $enrollment->installmentPlan->update(['status' => 'active']);
        }

        NotificationLog::create([
            'user_id' => $enrollment->user_id,
            'title'   => 'Course Access Restored',
            'message' => 'Your course access has been restored. ' . $reason,
            'type'    => 'success',
        ]);
    }
}
