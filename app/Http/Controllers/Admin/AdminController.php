<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;    
use App\Models\Ticket;   
use App\Models\User;     
use App\Models\Contact;  
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReplyContactMail;

class AdminController extends Controller
{
    public function index()
    {
        // --- 1. SỐ LIỆU CHO 4 THẺ THỐNG KÊ (TOP CARDS) ---

        // Thẻ 1: Doanh thu
        $totalRevenue = Order::where('status', 'paid')->sum('total_amount');

        // Thẻ 2: Đơn chờ (View mới dùng biến $pendingOrders làm Số lượng đếm)
        $pendingOrders = Order::where('status', 'pending')->count();

        // Thẻ 3: Tổng vé (QUAN TRỌNG: Đã đổi tên từ $ticketsCount thành $totalTickets)
        $totalTickets = Ticket::count();

        // Thẻ 4: Khách hàng (Bổ sung biến này vì View đang thiếu)
        $totalUsers = User::where('role', 'customer')->count();


        // --- 2. DỮ LIỆU BIỂU ĐỒ CỘT (DOANH THU 7 NGÀY) ---
        $chartStats = Order::where('status', 'paid')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->take(7)
            ->get();

        // Tách dữ liệu ra 2 mảng riêng để gửi sang Chart.js
        $revenueLabels = $chartStats->pluck('date')->toArray(); // Trục ngang: Ngày
        $revenueData = $chartStats->pluck('total')->toArray();  // Trục dọc: Tiền


        // --- 3. DỮ LIỆU BIỂU ĐỒ TRÒN (TRẠNG THÁI ĐƠN) ---
        // View cần biến $pieData dạng mảng: [Số đã trả, Số chờ, Số hủy]
        $pieData = [
            Order::where('status', 'paid')->count(),
            Order::where('status', 'pending')->count(),
            Order::where('status', 'cancelled')->count()
        ];


        // --- 4. TRẢ VỀ VIEW (Compact đủ các biến) ---
        return view('admin.dashboard', compact(
            'totalRevenue',
            'pendingOrders', // Số lượng đơn chờ
            'totalTickets',  // <--- Biến bạn đang bị lỗi
            'totalUsers',    // <--- Biến đếm User
            'revenueLabels', // Dữ liệu biểu đồ cột
            'revenueData',
            'pieData'        // Dữ liệu biểu đồ tròn
        ));

    }


    public function manageTickets()
    {
        // Lấy toàn bộ danh sách vé từ Database
        $tickets = Ticket::orderBy('created_at', 'desc')->get();

        // Trả về view kèm dữ liệu vé
        return view('admin.tickets.index', compact('tickets'));
    }

    // Hiển thị form thêm mới
    public function createTicket()
    {
        return view('admin.tickets.create');
    }

    // Lưu dữ liệu vào Database
    public function storeTicket(Request $request)
    {
        // Xác thực dữ liệu
        $request->validate([
            'title' => 'required',
            'price' => 'required|numeric',
        ]);

        // Lưu vào bảng tickets
        Ticket::create($request->all());

        return redirect()->route('admin.tickets')->with('success', 'Đã thêm vé VR mới thành công!');
    }

    //Quản lý vé
    public function manageOrders()
    {
        $orders = Order::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.orders.index', compact('orders'));
    }

    // Xử lý duyệt đơn hàng
    public function approveOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->status = 'paid'; // Chuyển sang trạng thái đã thanh toán
        $order->save();

        return back()->with('success', 'Đã duyệt đơn hàng #' . $id . ' thành công!');
    }
   
    //Quản lý người dùng
    public function manageUsers()
    {
        // Lấy tất cả user trừ chính Admin đang đăng nhập để tránh tự xóa mình
        $users = User::where('id', '!=', auth()->id())->get();
        return view('admin.users.index', compact('users'));
    }

    // Xử lý xóa người dùng
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'Đã xóa tài khoản người dùng thành công!');
    } 

    //Tin nhắn liên hệ 
    public function listContacts()
    {
        // Lấy danh sách tin nhắn, tin mới nhất xếp lên đầu
        $contacts = Contact::orderBy('created_at', 'desc')->get();

        // Trả về view kèm dữ liệu tin nhắn
        return view('admin.contacts.index', compact('contacts'));
    }

    // Xử lý xóa tin nhắn liên hệ
    public function deleteContact($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return back()->with('success', 'Đã xóa tin nhắn liên hệ thành công!');
    }
   
    //Phản hồi 
    public function replyContact(Request $request, $id)
    {
        $request->validate([
            'reply_content' => 'required|min:5'
        ]);

        $contact = Contact::findOrFail($id);

        // Gửi email
        Mail::to($contact->email)->send(new ReplyContactMail($request->reply_content, $contact->name));

        return redirect()->back()->with('success', 'Đã gửi phản hồi tới khách hàng thành công!');
    }
}