<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

//Quản lý đơn hàng
class BookingController extends Controller
{
    // 1. Danh sách đơn hàng
    public function index() {
        // Lấy danh sách từ bảng 'orders', kèm thông tin người dùng (user) và chi tiết (details)
        $bookings = Order::with(['user', 'details.ticket']) 
                         ->orderBy('created_at', 'desc')
                         ->paginate(10);
                         
        return view('admin.bookings.index', compact('bookings'));
    }

    // 2. Cập nhật trạng thái
    public function updateStatus(Request $request, $id) {
        $order = Order::findOrFail($id); // Tìm trong bảng orders
        $order->status = $request->status;
        $order->save();

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái đơn hàng #' . $id);
    }

    // 3. Xóa đơn hàng
    public function destroy($id) {
        $order = Order::findOrFail($id);
        
        // Xóa chi tiết đơn hàng trước
        if ($order->details) {
            $order->details()->delete();
        }
        
        $order->delete();

        return redirect()->back()->with('success', 'Đã xóa đơn hàng!');
    }
}