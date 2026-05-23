<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'enrollment.course']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($u) => $u->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($gateway = $request->input('gateway')) {
            $query->where('gateway', $gateway);
        }

        $payments = $query->latest()->paginate(20)->withQueryString();

        $totalRevenue  = Payment::where('status', 'success')->sum('amount');
        $todayRevenue  = Payment::where('status', 'success')
                                ->whereDate('paid_at', today())
                                ->sum('amount');
        $totalCount    = Payment::count();
        $pendingCount  = Payment::where('status', 'pending')->count();

        return view('admin.payment.index', compact(
            'payments',
            'totalRevenue',
            'todayRevenue',
            'totalCount',
            'pendingCount'
        ));
    }

    public function show(Payment $payment)
    {
        $payment->load(['user', 'enrollment.course', 'invoice']);
        return view('admin.payment.show', compact('payment'));
    }

    public function refund(Request $request, int $id)
    {
        $request->validate(['reason' => 'required|string|max:1000']);

        $payment = Payment::findOrFail($id);
        $payment->update([
            'status' => 'refunded',
            'notes'  => $request->input('reason'),
        ]);

        if ($payment->enrollment) {
            $payment->enrollment->update(['payment_status' => 'refunded']);
        }

        return back()->with('success', 'Payment refunded successfully.');
    }
}
