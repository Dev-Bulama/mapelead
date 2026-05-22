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
        ])->paginate(20);

        return view('admin.payment.installments.index', compact('installmentPlans'));
    }

    public function show(InstallmentPlan $installmentPlan)
    {
        $installmentPlan->load('enrollment.user', 'schedule');

        return view('admin.payment.installments.show', compact('installmentPlan'));
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
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        app(InstallmentService::class)->recordPayment($installmentPlan, $request->amount);

        return redirect()->back()->with('success', 'Payment recorded successfully.');
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
