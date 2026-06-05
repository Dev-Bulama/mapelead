<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchEnrollment;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\Payment\InstallmentService;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function __construct(private InstallmentService $installmentService) {}

    public function index(Request $request)
    {
        $query = Enrollment::with([
                'user', 'course', 'batch.batch',
                'installmentPlan' => fn($q) => $q->withCount([
                    'schedule as paid_installments' => fn($q) => $q->where('status', 'paid'),
                ]),
            ])
            ->orderByDesc('enrolled_at');

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('admission_number', 'like', "%$search%")
                    ->orWhere('first_name', 'like', "%$search%")
                    ->orWhere('last_name',  'like', "%$search%")
                    ->orWhere('email',      'like', "%$search%"))
                  ->orWhereHas('course', fn($c) => $c->where('title', 'like', "%$search%"));
            });
        }

        if ($course = $request->course_id) {
            $query->where('course_id', $course);
        }

        if ($request->batch_id) {
            $query->whereHas('batch', fn($q) => $q->where('batch_id', $request->batch_id));
        }

        if ($payStatus = $request->payment_status) {
            $query->where('payment_status', $payStatus);
        }

        if ($status = $request->status) {
            if ($status === 'locked') {
                $query->where('access_locked', true);
            } else {
                $query->where('status', $status)->where('access_locked', false);
            }
        }

        $enrollments = $query->paginate(20)->withQueryString();
        $courses = Course::orderBy('title')->get(['id', 'title']);
        $batches = Batch::orderBy('name')->get(['id', 'name']);

        $stats = [
            'total'    => Enrollment::count(),
            'active'   => Enrollment::where('status', 'active')->where('access_locked', false)->count(),
            'pending'  => Enrollment::where('status', 'pending')->count(),
            'locked'   => Enrollment::where('access_locked', true)->count(),
            'completed'=> Enrollment::where('status', 'completed')->count(),
        ];

        return view('admin.admissions.index', compact('enrollments', 'courses', 'batches', 'stats'));
    }

    public function show(Enrollment $enrollment)
    {
        $enrollment->load([
            'user', 'course.instructor.user', 'course.category',
            'batch.batch', 'installmentPlan.schedule',
            'payments', 'certificate',
        ]);

        $availableBatches = Batch::where('course_id', $enrollment->course_id)
            ->where('status', 'active')
            ->get();

        return view('admin.admissions.show', compact('enrollment', 'availableBatches'));
    }

    public function edit(Enrollment $enrollment)
    {
        $enrollment->load(['user', 'course', 'batch.batch']);
        $availableBatches = Batch::where('course_id', $enrollment->course_id)
            ->where('status', 'active')
            ->get();

        return view('admin.admissions.edit', compact('enrollment', 'availableBatches'));
    }

    public function update(Request $request, Enrollment $enrollment)
    {
        $request->validate([
            'status'         => 'required|in:pending,active,completed,cancelled',
            'payment_status' => 'required|in:unpaid,paid,partial,refunded',
            'amount_paid'    => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $enrollment->update($request->only('status', 'payment_status', 'amount_paid'));

        return redirect()->route('admin.enrollments.show', $enrollment)
            ->with('success', 'Enrollment updated successfully.');
    }

    public function approve(Enrollment $enrollment)
    {
        $enrollment->update([
            'status'        => 'active',
            'access_locked' => false,
            'access_locked_at'     => null,
            'access_locked_reason' => null,
        ]);

        return back()->with('success', "Enrollment #{$enrollment->id} approved — access granted.");
    }

    public function reject(Request $request, Enrollment $enrollment)
    {
        $enrollment->update([
            'status'        => 'cancelled',
            'access_locked' => true,
            'access_locked_at'     => now(),
            'access_locked_reason' => $request->reason ?? 'Enrollment rejected by administrator.',
        ]);

        return back()->with('success', "Enrollment #{$enrollment->id} rejected.");
    }

    public function suspend(Request $request, Enrollment $enrollment)
    {
        $enrollment->update([
            'access_locked'        => true,
            'access_locked_at'     => now(),
            'access_locked_reason' => $request->reason ?? 'Access suspended by administrator.',
        ]);

        return back()->with('success', "Access suspended for enrollment #{$enrollment->id}.");
    }

    public function unlock(Enrollment $enrollment)
    {
        $enrollment->update([
            'access_locked'        => false,
            'access_locked_at'     => null,
            'access_locked_reason' => null,
        ]);

        return back()->with('success', "Access restored for enrollment #{$enrollment->id}.");
    }

    public function reassignBatch(Request $request, Enrollment $enrollment)
    {
        $request->validate(['batch_id' => 'nullable|exists:batches,id']);

        // Remove from current batch
        BatchEnrollment::where('enrollment_id', $enrollment->id)->delete();

        if ($request->batch_id) {
            $batch = Batch::findOrFail($request->batch_id);

            if ($batch->max_students && $batch->current_students >= $batch->max_students) {
                return back()->with('error', 'Selected batch is at full capacity.');
            }

            BatchEnrollment::create([
                'batch_id'     => $batch->id,
                'enrollment_id'=> $enrollment->id,
                'joined_at'    => now(),
            ]);

            $batch->increment('current_students');
        }

        return back()->with('success', 'Batch reassigned successfully.');
    }

    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();
        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment deleted.');
    }
}
