<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;

//Giỏ hàng
class CartController extends Controller
{
    // 1. Thêm vào giỏ
    public function addToCart($id)
    {
        $ticket = Ticket::findOrFail($id);
        $cart = session()->get('cart', []);

        // Nếu vé đã có trong giỏ -> Tăng số lượng
        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            // Nếu chưa -> Thêm mới
            $cart[$id] = [
                "name" => $ticket->name,
                "quantity" => 1,
                "price" => $ticket->price, // Mặc định lấy giá ngày thường
                "image" => $ticket->image_url
            ];
        }

        session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Đã thêm vé vào giỏ!');
    }

    // 2. Xem giỏ hàng
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }

    public function remove($id)
    {
        $cart = session()->get('cart');

        if(isset($cart[$id])) {
            unset($cart[$id]); // Xóa khỏi mảng
            session()->put('cart', $cart); // Lưu lại session
        }

        return redirect()->back()->with('success', 'Đã xóa vé khỏi giỏ hàng!');
    }

    // 5. Xóa sạch giỏ hàng
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('ticket.shop')->with('success', 'Đã xóa sạch giỏ hàng!');
    }

    public function update(Request $request)
    {
        if ($request->id && $request->quantity) {
            $cart = session()->get('cart');

            // 1. Cập nhật số lượng
            $cart[$request->id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);

            // 2. Tính lại tiền
            $itemSubtotal = $cart[$request->id]['price'] * $cart[$request->id]['quantity'];

            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            // 3. Trả về kết quả JSON (Quan trọng: tên biến phải khớp với file View)
            return response()->json([
                'success' => true,
                'item_subtotal' => number_format($itemSubtotal) . 'đ', // Tiền từng món
                'grand_total' => number_format($total) . 'đ'           // Tổng cộng
            ]);
        }
    }
}