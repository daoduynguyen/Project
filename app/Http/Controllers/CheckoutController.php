<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\Ticket;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    // BƯỚC 1: Hiển thị Form nhập thông tin 
    public function bookingForm($id)
    {
        $ticket = Ticket::findOrFail($id);

        $fakeCart = [
            $ticket->id => [
                "name" => $ticket->name,
                "quantity" => 1,
                "price" => $ticket->price,
                "image" => $ticket->image ?? $ticket->image_url,
            ]
        ];

        // Tính sẵn giá ngày thường/cuối tuần để View hiển thị
        $totalWeek = $ticket->price;
        $totalWeekend = ($ticket->price_weekend > 0) ? $ticket->price_weekend : $ticket->price;

        // Truyền ticket_id sang view để Form biết đang đặt vé nào
        return view('checkout.index', [
            'cart' => $fakeCart,
            'totalWeek' => $totalWeek,
            'totalWeekend' => $totalWeekend,
            'ticket_id' => $id
        ]);
    }

    // BƯỚC 2: Xử lý thông tin -> Lưu vào Session Giỏ hàng -> Chuyển sang trang Review
    public function confirmToCart(Request $request)
    {
        $request->validate([
            'booking_date' => 'required|date|after_or_equal:today',
            'customer_name' => 'required',
            'customer_phone' => 'required',
            'ticket_id' => 'required'
        ]);

        $ticket = Ticket::findOrFail($request->ticket_id);

        // 1. Tính giá chuẩn dựa trên ngày khách chọn
        $date = Carbon::parse($request->booking_date);
        $isWeekend = $date->isWeekend();

        $price = $ticket->price; // Mặc định giá thường
        if ($isWeekend && $ticket->price_weekend > 0) {
            $price = $ticket->price_weekend;
        }

        // 2. Tạo Key cho giỏ hàng (ID vé + Ngày) để khách có thể đặt nhiều vé khác ngày
        $cartKey = $ticket->id . '_' . $date->format('Ymd');

        // 3. Chuẩn bị dữ liệu Item
        $cartItem = [
            "id" => $ticket->id,
            "name" => $ticket->name,
            "quantity" => 1, // Mặc định là 1, sang trang giỏ hàng khách có thể sửa số lượng
            "price" => $price, // Giá này ĐÃ được tính theo ngày (chuẩn)
            "image" => $ticket->image ?? $ticket->image_url,
            "booking_date" => $request->booking_date, // Lưu ngày khách chọn

            // Lưu thông tin khách vào mảng phụ để dùng sau
            "customer_info" => $request->only(['customer_name', 'customer_phone', 'payment_method', 'shipping_address', 'note'])
        ];

        // 4. Đẩy vào Session Cart
        $cart = session()->get('cart', []);
        $cart[$cartKey] = $cartItem;
        session()->put('cart', $cart);

        // 5. Chuyển sang trang Giỏ hàng (Trang Review)
        return redirect()->route('cart.index')->with('success', 'Đã lưu thông tin vé! Vui lòng kiểm tra lại đơn hàng.');
    }

    // BƯỚC 4: Xử lý thanh toán cuối cùng (Từ trang Giỏ hàng bấm Chốt đơn)
    public function finalPayment()
    {
        $cart = session('cart');
        if (!$cart)
            return redirect()->route('home')->with('error', 'Giỏ hàng trống');

        DB::beginTransaction();
        try {
            // 1. Tính Tổng tiền hàng (Gốc)
            $totalAmount = 0;
            foreach ($cart as $item) {
                $totalAmount += $item['price'] * $item['quantity'];
            }

            // ====================================================
            //  XỬ LÝ VOUCHER TỪ SESSION 
            // ====================================================
            $discountAmount = 0;
            $couponCode = null;

            // Kiểm tra xem có mã giảm giá trong Session không
            if (session()->has('coupon')) {
                $couponSession = session('coupon');
                $discountAmount = $couponSession['discount'];
                $couponCode = $couponSession['code'];

                // Logic an toàn: Nếu tiền giảm > Tổng tiền thì chỉ giảm bằng đúng tổng tiền (tránh âm tiền)
                if ($discountAmount > $totalAmount) {
                    $discountAmount = $totalAmount;
                }
            }

            // Tính số tiền thực tế khách phải trả
            $finalTotal = $totalAmount - $discountAmount;

            // --- XỬ LÝ THÔNG TIN KHÁCH ---
            $firstItem = reset($cart);
            $info = $firstItem['customer_info'] ?? [];
            $user = Auth::user();

            $cName = !empty($info['customer_name']) ? $info['customer_name'] : ($user->name ?? 'Khách vãng lai');
            $cPhone = !empty($info['customer_phone']) ? $info['customer_phone'] : ($user->phone ?? '');
            $cAddress = !empty($info['shipping_address']) ? $info['shipping_address'] : ($user->address ?? 'Nhận vé qua Email');
            $cPayment = !empty($info['payment_method']) ? $info['payment_method'] : 'cod';
            $cNote = !empty($info['note']) ? $info['note'] : '';


            // 2. Tạo đơn hàng (Lưu các giá trị mới tính toán)
            $order = Order::create([
                'user_id' => Auth::id(),
                'customer_name' => $cName,
                'customer_phone' => $cPhone,
                'shipping_address' => $cAddress,

                'total_amount' => $finalTotal,      // <---  Lưu số tiền thực trả (đã trừ giảm giá)
                'discount_amount' => $discountAmount, // <---  Lưu số tiền được giảm (nếu database có cột này)
                'coupon_code' => $couponCode,       // <--- Lưu mã code đã dùng (nếu database có cột này)

                'payment_method' => $cPayment,
                'status' => 'pending',
                'note' => $cNote,
                'booking_date' => $firstItem['booking_date'] ?? Carbon::tomorrow()->format('Y-m-d'),
            ]);

            // 3. Tạo chi tiết đơn 
            foreach ($cart as $key => $details) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'ticket_id' => $details['id'],
                    'ticket_name' => $details['name'],
                    'quantity' => $details['quantity'],
                    'price' => $details['price']
                ]);
            }

            // 4. Xóa Session
            session()->forget('cart');
            session()->forget('coupon'); // Xóa mã giảm giá sau khi dùng xong

            DB::commit();

            return redirect()->route('profile.index')->with([
                'payment_success' => true,
                'order_id' => $order->id,
                'amount' => $finalTotal, // Hiển thị số tiền thực trả      
                // 2 DÒNG NÀY ĐỂ GỬI SANG VIEW 
                'discount_amount' => $discountAmount,
                'coupon_code' => $couponCode
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    public function checkCoupon(Request $request)
    {
        // 1. Lấy dữ liệu gửi lên
        $code = $request->code;
        $totalAmount = $request->total_amount;

        // 2. Tìm mã trong database
        $coupon = Coupon::where('code', $code)->first();

        // 3. Kiểm tra các điều kiện
        if (!$coupon) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mã giảm giá không tồn tại!'
            ]);
        }

        if ($coupon->expiration_date && Carbon::now()->gt($coupon->expiration_date)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mã này đã hết hạn sử dụng!'
            ]);
        }

        if ($coupon->quantity <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mã này đã hết lượt sử dụng!'
            ]);
        }

        // 4. Tính toán số tiền giảm
        $discount = 0;
        if ($coupon->type == 'percent') {
            $discount = ($totalAmount * $coupon->value) / 100;
        } else {
            $discount = $coupon->value;
        }
        if ($discount > $totalAmount)
            $discount = $totalAmount;
        $newTotal = $totalAmount - $discount;

        // ===  LƯU VÀO SESSION  ===
        session()->put('coupon', [
            'code' => $coupon->code,
            'discount' => $discount,
            'type' => $coupon->type,
            'value' => $coupon->value
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Áp dụng mã thành công!',
            'discount' => $discount,
            'new_total' => $newTotal
        ]);

    }

    public function removeCoupon()
    {
        session()->forget('coupon'); // Xóa session coupon
        return back()->with('success', 'Đã gỡ mã giảm giá thành công!'); // Quay lại trang cũ
    }
}