<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

//Vocher
class CouponController extends Controller
{
    // 1. Danh sách Voucher
    public function index()
    {
        $coupons = Coupon::latest()->paginate(10);
        return view('admin.coupons.index', compact('coupons'));
    }

    // 2. Form thêm mới
    public function create()
    {
        return view('admin.coupons.create');
    }

    // 3. Lưu Voucher mới
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code|uppercase', // Mã không trùng, viết hoa
            'type' => 'required',
            'value' => 'required|numeric|min:0',
            'expiry_date' => 'required|date|after:today', // Phải là ngày tương lai
        ], [
            'code.unique' => 'Mã này đã tồn tại!',
            'expiry_date.after' => 'Hạn sử dụng phải lớn hơn hôm nay.'
        ]);

        Coupon::create($request->all());

        return redirect()->route('admin.coupons.index')->with('success', 'Đã thêm mã giảm giá thành công!');
    }

    // 4. Form sửa
    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('admin.coupons.edit', compact('coupon'));
    }

    // 5. Cập nhật Voucher
    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        
        $request->validate([
            'code' => 'required|unique:coupons,code,'.$id, // Cho phép trùng mã của chính nó
            'value' => 'required|numeric|min:0',
            'expiry_date' => 'required|date',
        ]);

        $coupon->update($request->all());

        return redirect()->route('admin.coupons.index')->with('success', 'Cập nhật thành công!');
    }

    // 6. Xóa Voucher
    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('success', 'Đã xóa mã giảm giá!');
    }
}