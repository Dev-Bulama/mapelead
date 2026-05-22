<?php
namespace App\Console\Commands;

use App\Jobs\SendPaymentReminder;
use App\Models\InstallmentPlan;
use App\Models\InstallmentSchedule;
use App\Services\Payment\InstallmentService;
use Illuminate\Console\Command;

class ProcessOverduePayments extends Command
{
    protected $signature = 'payments:process-overdue';
    protected $description = 'Lock access for overdue installment payments and send reminders';

    public function handle(InstallmentService $service): int
    {
        $this->info('Processing overdue payments...');
        $locked = $service->processOverdue();
        $this->info("Locked {$locked} enrollment(s) due to overdue payments.");

        $this->info('Dispatching payment reminders...');
        $this->dispatchReminders();

        return 0;
    }

    private function dispatchReminders(): void
    {
        $plans = InstallmentPlan::where('status', 'active')
            ->where('outstanding_balance', '>', 0)
            ->with('schedule')
            ->get();

        $reminderCount = 0;

        foreach ($plans as $plan) {
            $nextSchedule = InstallmentSchedule::where('plan_id', $plan->id)
                ->where('status', 'pending')
                ->orderBy('due_date')
                ->first();

            if (!$nextSchedule) continue;

            $daysUntilDue = now()->diffInDays($nextSchedule->due_date, false);

            $reminderType = match (true) {
                $daysUntilDue < 0  => 'overdue',
                $daysUntilDue === 0 => 'due_today',
                $daysUntilDue === 1 => '1_day',
                $daysUntilDue === 3 => '3_days',
                $daysUntilDue === 7 => '7_days',
                default             => null,
            };

            if ($reminderType) {
                SendPaymentReminder::dispatch($plan->id, $reminderType);
                $reminderCount++;
            }
        }

        $this->info("Dispatched {$reminderCount} reminder(s).");
    }
}
