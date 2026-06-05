<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstallmentPlan;
use App\Models\InstallmentSchedule;
use App\Models\Enrollment;
use App\Models\Course;
use App\Services\Payment\InstallmentService;
use Illuminate\Http\Request;

class InstallmentController extends Controller
{
    public function index()
    {
        $installmentPlans = InstallmentPlan::with([
            'enrollment.user',
            'enrollment.course',
        ])->withCount([
            'schedule as paid_count'    => fn($q) => $q->where('status', 'paid'),
            'schedule as total_count',
        ])->latest()->paginate(20);

        $globalStats = [
            'total'     => InstallmentPlan::count(),
            'active'    => InstallmentPlan::where('status', 'active')->count(),
            'overdue'   => InstallmentPlan::where('status', 'overdue')->count(),
            'completed' => InstallmentPlan::where('status', 'completed')->count(),
            'revenue'   => InstallmentPlan::sum('amount_paid'),
            'outstanding'=> InstallmentPlan::sum('outstanding_balance'),
        ];

        return view('admin.payment.installments.index', compact('installmentPlans', 'globalStats'));
    }

    public function show(InstallmentPlan $installmentPlan)
    {
        $installmentPlan->load('enrollment.user', 'enrollment.course', 'schedule');
        $payments = \App\Models\Payment::where('enrollment_id', $installmentPlan->enrollment_id)
            ->orderByDesc('created_at')->get();

        return view('admin.payment.installments.show', compact('installmentPlan', 'payments'));
    }

    public function create()
    {
        $courses = Course::where('status', 'active')->get();

        return view('admin.payment.installments.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'enrollment_id'     => 'required|exists:enrollments,id',
            'total_amount'      => 'required|numeric|min:0',
            'down_payment'      => 'required|numeric|min:0',
            'installment_count' => 'required|integer|min:1|max:24',
            'first_due_date'    => 'required|date',
            'grace_period_days' => 'required|integer|min:0|max:30',
        ]);

        $enrollment = Enrollment::findOrFail($validated['enrollment_id']);

        app(InstallmentService::class)->createPlan($enrollment, $validated);

        return redirect()->route('admin.installments.index')
            ->with('success', 'Installment plan created successfully.');
    }

    public function recordPayment(Request $request, InstallmentPlan $installmentPlan)
    {
        $validated = $request->validate([
            'amount'  => 'required|numeric|min:1',
            'notes'   => 'nullable|string|max:255',
            'gateway' => 'nullable|string|max:50',
        ]);

        $enrollment = $installmentPlan->enrollment;

        // Create a payment ledger record so it appears in Payments and student history
        \App\Models\Payment::create([
            'user_id'       => $enrollment->user_id,
            'enrollment_id' => $enrollment->id,
            'reference'     => 'INST-' . strtoupper(\Illuminate\Support\Str::random(10)),
            'gateway'       => $validated['gateway'] ?? 'manual',
            'amount'        => $validated['amount'],
            'currency'      => 'NGN',
            'status'        => 'success',
            'payment_method'=> 'manual',
            'notes'         => $validated['notes'] ?: 'Installment payment (admin recorded)',
            'paid_at'       => now(),
        ]);

        app(InstallmentService::class)->recordPayment($installmentPlan, (float) $validated['amount']);

        return redirect()->back()->with('success', 'Payment of ₦' . number_format($validated['amount']) . ' recorded successfully.');
    }

    public function unlock(InstallmentPlan $installmentPlan)
    {
        app(InstallmentService::class)->unlockAccess(
            $installmentPlan->enrollment,
            'Manually unlocked by admin'
        );

        return redirect()->back()->with('success', 'Access unlocked successfully.');
    }

    public function destroy(InstallmentPlan $installmentPlan)
    {
        $installmentPlan->delete();

        return redirect()->back()->with('success', 'Installment plan deleted successfully.');
    }
}
