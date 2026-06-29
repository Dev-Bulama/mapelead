<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->paginate(20);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'             => 'required|string|max:50|unique:coupons,code',
            'description'      => 'nullable|string|max:255',
            'type'             => 'required|in:percentage,fixed',
            'value'            => 'required|numeric|min:0.01',
            'minimum_order'    => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit'      => 'nullable|integer|min:1',
            'starts_at'        => 'nullable|date',
            'expires_at'       => 'nullable|date|after_or_equal:starts_at',
            'is_active'        => 'boolean',
        ]);

        $data['code']      = strtoupper(trim($data['code']));
        $data['is_active'] = $request->boolean('is_active', true);

        Coupon::create($data);
        return back()->with('success', 'Coupon created successfully.');
    }

    public function update(Request $request, Coupon $coupon)
    {
        $data = $request->validate([
            'code'             => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'description'      => 'nullable|string|max:255',
            'type'             => 'required|in:percentage,fixed',
            'value'            => 'required|numeric|min:0.01',
            'minimum_order'    => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit'      => 'nullable|integer|min:1',
            'starts_at'        => 'nullable|date',
            'expires_at'       => 'nullable|date|after_or_equal:starts_at',
            'is_active'        => 'boolean',
        ]);

        $data['code']      = strtoupper(trim($data['code']));
        $data['is_active'] = $request->boolean('is_active');

        $coupon->update($data);
        return back()->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'Coupon deleted.');
    }
}
