<?php
namespace App\Jobs;

use App\Models\InstallmentPlan;
use App\Models\NotificationLog;
use App\Services\CMS\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPaymentReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly int $planId,
        public readonly string $reminderType
    ) {}

    public function handle(EmailTemplateService $emailService): void
    {
        $plan = InstallmentPlan::with(['enrollment.user', 'enrollment.course'])->find($this->planId);

        if (!$plan || $plan->status === 'completed') return;

        $enrollment = $plan->enrollment;
        if (!$enrollment) return;

        $user   = $enrollment->user;
        $course = $enrollment->course;

        $nextDue = $plan->nextDue();
        if (!$nextDue) return;

        $message = match ($this->reminderType) {
            '7_days'   => "Your next installment of ₦" . number_format($nextDue->amount, 2) . " for {$course->title} is due in 7 days ({$nextDue->due_date->format('M d, Y')}).",
            '3_days'   => "Your next installment of ₦" . number_format($nextDue->amount, 2) . " for {$course->title} is due in 3 days ({$nextDue->due_date->format('M d, Y')}).",
            '1_day'    => "Your next installment of ₦" . number_format($nextDue->amount, 2) . " for {$course->title} is due tomorrow.",
            'due_today'=> "Your installment of ₦" . number_format($nextDue->amount, 2) . " for {$course->title} is due TODAY.",
            'overdue'  => "Your installment payment for {$course->title} is OVERDUE. Please pay ₦" . number_format($plan->outstanding_balance, 2) . " to avoid course access suspension.",
            default    => "You have a pending payment for {$course->title}.",
        };

        // In-app notification
        NotificationLog::create([
            'user_id' => $user->id,
            'title'   => 'Payment Reminder',
            'message' => $message,
            'type'    => $this->reminderType === 'overdue' ? 'danger' : 'warning',
        ]);

        // Email notification
        $variables = [
            'student_name'     => $user->full_name,
            'balance'          => number_format($plan->outstanding_balance, 2),
            'due_date'         => $nextDue->due_date->format('M d, Y'),
            'course_name'      => $course->title,
            'admission_number' => $user->admission_number ?? 'N/A',
            'amount_due'       => number_format($nextDue->amount, 2),
        ];

        $emailService->send('payment_reminder', $user, $variables);
    }
}
