<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\InstallmentPlan;
use App\Models\InstallmentSchedule;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InstallmentDemoSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::whereIn('status', ['active', 'published'])->first();
        if (! $course) {
            $this->command->warn('No active/published course found. Skipping InstallmentDemoSeeder.');
            return;
        }

        // Scenario A: student making active installment payments (3 of 6 paid)
        $userA = $this->findOrCreateStudent('Amaka Okonkwo', 'amaka.okonkwo@demo.test');
        $this->createInstallmentScenario($userA, $course, [
            'total'       => 150000,
            'down'        => 30000,
            'count'       => 6,
            'paid_count'  => 3,       // 3 installments already paid
            'first_due'   => now()->subMonths(3),
            'status'      => 'active',
        ]);

        // Scenario B: student who is overdue (missed 1 payment)
        $userB = $this->findOrCreateStudent('Chukwuemeka Eze', 'chukwuemeka.eze@demo.test');
        $this->createInstallmentScenario($userB, $course, [
            'total'       => 120000,
            'down'        => 20000,
            'count'       => 4,
            'paid_count'  => 1,
            'first_due'   => now()->subMonths(3),
            'status'      => 'overdue',
        ]);

        // Scenario C: completed plan (all installments paid)
        $userC = $this->findOrCreateStudent('Fatima Aliyu', 'fatima.aliyu@demo.test');
        $this->createInstallmentScenario($userC, $course, [
            'total'       => 80000,
            'down'        => 20000,
            'count'       => 3,
            'paid_count'  => 3,
            'first_due'   => now()->subMonths(4),
            'status'      => 'completed',
        ]);

        $this->command->info('InstallmentDemoSeeder: created 3 sample installment scenarios.');
    }

    private function findOrCreateStudent(string $name, string $email): User
    {
        $existing = User::where('email', $email)->first();
        if ($existing) return $existing;

        [$first, $last] = explode(' ', $name, 2);
        $user = User::create([
            'first_name'        => $first,
            'last_name'         => $last,
            'email'             => $email,
            'password'          => Hash::make('Demo@12345'),
            'email_verified_at' => now(),
        ]);
        $user->assignRole('student');
        return $user;
    }

    private function createInstallmentScenario(User $user, Course $course, array $opts): void
    {
        $total      = $opts['total'];
        $down       = $opts['down'];
        $count      = $opts['count'];
        $paidCount  = $opts['paid_count'];
        $firstDue   = Carbon::parse($opts['first_due']);
        $planStatus = $opts['status'];

        // Don't create duplicate enrollment for same user+course
        $enrollment = Enrollment::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            [
                'status'         => 'active',
                'payment_status' => $planStatus === 'completed' ? 'paid' : 'partial',
                'payment_type'   => 'installment',
                'training_type'  => 'online',
                'amount_paid'    => $down,
                'enrolled_at'    => $firstDue->copy()->subDays(3),
                'access_locked'  => $planStatus === 'overdue',
            ]
        );

        // Skip if plan already exists
        if (InstallmentPlan::where('enrollment_id', $enrollment->id)->exists()) {
            return;
        }

        $remaining     = $total - $down;
        $installAmt    = round($remaining / $count, 2);
        $paidAmount    = $down + ($installAmt * $paidCount);
        $balance       = max(0, $total - $paidAmount);

        $plan = InstallmentPlan::create([
            'enrollment_id'       => $enrollment->id,
            'user_id'             => $user->id,
            'course_id'           => $course->id,
            'total_amount'        => $total,
            'down_payment'        => $down,
            'amount_paid'         => $paidAmount,
            'outstanding_balance' => $balance,
            'installment_count'   => $count,
            'grace_period_days'   => 3,
            'auto_lock_on_overdue'=> true,
            'status'              => $planStatus,
        ]);

        // Record down payment
        Payment::create([
            'user_id'       => $user->id,
            'enrollment_id' => $enrollment->id,
            'reference'     => 'DEMO-DP-' . strtoupper(Str::random(8)),
            'gateway'       => 'paystack',
            'amount'        => $down,
            'currency'      => 'NGN',
            'status'        => 'success',
            'payment_method'=> 'card',
            'notes'         => 'Down payment (demo)',
            'paid_at'       => $firstDue->copy()->subDays(3),
        ]);

        // Create installment schedules
        $dueDate = $firstDue->copy();
        for ($i = 1; $i <= $count; $i++) {
            $isPaid    = $i <= $paidCount;
            $isOverdue = !$isPaid && $dueDate->isPast() && $planStatus === 'overdue';

            $schedule = InstallmentSchedule::create([
                'plan_id'            => $plan->id,
                'installment_number' => $i,
                'amount'             => $installAmt,
                'due_date'           => $dueDate->toDateString(),
                'status'             => $isPaid ? 'paid' : ($isOverdue ? 'overdue' : 'pending'),
                'amount_paid'        => $isPaid ? $installAmt : 0,
                'paid_at'            => $isPaid ? $dueDate->copy()->subDays(rand(0, 3)) : null,
            ]);

            // Record payment transaction for paid installments
            if ($isPaid) {
                Payment::create([
                    'user_id'       => $user->id,
                    'enrollment_id' => $enrollment->id,
                    'reference'     => 'DEMO-INST-' . $plan->id . '-' . $i . '-' . strtoupper(Str::random(6)),
                    'gateway'       => 'paystack',
                    'amount'        => $installAmt,
                    'currency'      => 'NGN',
                    'status'        => 'success',
                    'payment_method'=> 'card',
                    'notes'         => "Installment #{$i} (demo)",
                    'paid_at'       => $schedule->paid_at,
                ]);
            }

            $dueDate->addMonth();
        }

        // Lock access if overdue
        if ($planStatus === 'overdue') {
            $enrollment->update([
                'access_locked'        => true,
                'access_locked_at'     => now()->subDays(5),
                'access_locked_reason' => 'Payment overdue. Complete your installment payment to regain access.',
            ]);
        }

        if ($planStatus === 'completed') {
            $enrollment->update(['payment_status' => 'paid', 'access_locked' => false]);
        }
    }
}
