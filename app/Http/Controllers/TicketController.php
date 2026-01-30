<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Ticket;
use App\Models\Booking;
use App\Models\BookingDetail;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon; 

class TicketController extends Controller
{
    // ---------------------------------------------------------
    // 1. TRANG CHỦ: Hiển thị 6 vé HOT nhất (Cả Active & Maintenance)
    // ---------------------------------------------------------
    public function index()
    {
        $tickets = Ticket::with(['category', 'location'])
                        // SỬA: Lấy cả Active và Maintenance để hiện thông báo bảo trì
                        ->whereIn('status', ['active', 'maintenance'])
                        ->orderBy('play_count', 'desc') // Sắp xếp lượt chơi giảm dần
                        ->take(6) // Chỉ lấy 6 bản ghi
                        ->get();

        return view('home', compact('tickets'));
    }

    // ---------------------------------------------------------
    // 2. TRANG CỬA HÀNG: Hiển thị TẤT CẢ (Có phân trang)
    // ---------------------------------------------------------
    public function shop(Request $request)
    {
        // 1. Khởi tạo truy vấn cơ bản (Lấy vé Active và đang Bảo trì)
        $query = Ticket::with(['category', 'location'])
                       ->whereIn('status', ['active', 'maintenance']);

        // 2. Thiết lập tiêu đề mặc định
        $locationName = "Tất cả vé";

        // 3. LOGIC LỌC: Kiểm tra xem có chọn cơ sở nào không?
        if ($request->has('location_id') && $request->location_id != null) {
            
            // Lọc theo ID cơ sở
            $query->where('location_id', $request->location_id);
            
            // Tìm tên cơ sở để hiển thị tiêu đề cho đẹp
            $loc = Location::find($request->location_id);
            if ($loc) {
                $locationName = "Vé tại " . $loc->name;
            }
        }

        // 4. SẮP XẾP & PHÂN TRANG
        // Yêu cầu của bạn: "sắp xếp theo mức độ hot từ cao xuống thấp"
        $tickets = $query->orderBy('play_count', 'desc') 
                         ->paginate(9); 

        // 5. Trả về View kèm theo danh sách vé và tên tiêu đề
        return view('shop', compact('tickets', 'locationName'));
    }

    // ---------------------------------------------------------
    // 3. TRANG CHI TIẾT SẢN PHẨM
    // ---------------------------------------------------------
    public function show($id)
    {
        // Vẫn tìm vé bình thường dù đang bảo trì (để hiện thông tin)
        $ticket = Ticket::with(['category', 'location'])->findOrFail($id);
        
        // Gợi ý 3 game KHÁC cùng loại
        $related = Ticket::where('category_id', $ticket->category_id)
                        ->where('id', '!=', $id)
                        ->whereIn('status', ['active', 'maintenance']) // Gợi ý cả game bảo trì cũng được
                        ->limit(3)
                        ->get();
        
        return view('detail', compact('ticket', 'related'));
    }

    // ---------------------------------------------------------
// 4. XỬ LÝ ĐẶT VÉ (Đẩy vào giỏ hàng)
    // ---------------------------------------------------------
    public function create($id)
    {
        $ticket = Ticket::findOrFail($id);

        // 1. Kiểm tra bảo trì
        if ($ticket->status == 'maintenance') {
            return redirect()->route('ticket.show', $id)
                             ->with('error', 'Trò chơi đang bảo trì, không thể đặt vé lúc này!');
        }

        // 2. Thay vì return view('booking'), ta gọi logic thêm vào giỏ hàng (Cart)
        // Điều này giúp tận dụng Controller Cart đã viết ở bước trước
        return redirect()->route('cart.add', ['id' => $id]);
    }

    // ---------------------------------------------------------
    // 5. XỬ LÝ LƯU ĐƠN HÀNG (Có chặn bảo trì)
    // ---------------------------------------------------------
    public function store(Request $request)
    {
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_phone' => 'required|string|max:15',
            'quantity'       => 'required|integer|min:1|max:50',
        ]);

        DB::beginTransaction();
        try {
            $ticket = Ticket::findOrFail($request->ticket_id);
            
            // BẢO MẬT: Chặn lần cuối ở server
            if ($ticket->status == 'maintenance') {
                return back()->with('error', 'Trò chơi này đang bảo trì!');
            }

            // Tính giá
            $today = Carbon::now();
            
            if ($today->isWeekend()) {
                $pricePerTicket = $ticket->price_weekend;
            } else {
                $pricePerTicket = $ticket->price;
            }

            $totalAmount = $pricePerTicket * $request->quantity;

            // Lưu Booking
            $booking = Booking::create([
                'user_id'        => null, // Khách vãng lai
                'customer_name'  => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'total_amount'   => $totalAmount,
                'status'         => 'pending',
                'payment_method' => 'cod',
                'booking_date'   => now(),
            ]);

            // Lưu Booking Detail
            BookingDetail::create([
                'booking_id' => $booking->id,
                'ticket_id'  => $ticket->id,
                'quantity'   => $request->quantity,
                'price'      => $pricePerTicket,
            ]);

            DB::commit();
            return redirect()->route('home')->with('success', 'Đặt vé thành công! Mã đơn: ' . $booking->id . ' - Tổng tiền: ' . number_format($totalAmount) . 'đ');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------
    // 6. CÁC TRANG TĨNH
    // ---------------------------------------------------------
    public function about()
    {
        return view('about');
    }

}