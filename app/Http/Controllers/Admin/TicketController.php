<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Category;
use App\Models\Location;

class TicketController extends Controller
{
    // Danh sách vé
    public function index()
    {
        $tickets = Ticket::orderBy('id', 'desc')->paginate(5); 

        return view('admin.tickets.index', compact('tickets'));
    }

    // Form thêm vé
    public function create()
    {
        $categories = Category::all();
        $locations  = Location::all();
        return view('admin.tickets.create', compact('categories', 'locations'));
    }

    // --- HÀM LƯU DỮ LIỆU ---
    public function store(Request $request) {
        // 1. Validate: 
        $request->validate([
            'name' => 'required',
            'location_name' => 'required|string', 
            'price' => 'required|numeric|min:0',  
            'price_weekend' => 'nullable|numeric|min:0', 
            'category_id' => 'required',
            'image_url' => 'required',
        ]);

        // 2. Logic Tự động tạo cơ sở
       $location = Location::firstOrCreate(
        ['name' => $request->location_name], // Điều kiện tìm (Tên cơ sở)
        [
            'address' => 'Đang cập nhật',    // Địa chỉ mặc định
            'hotline' => '0909000000',       
        ]
    );

        // 3. Lưu vé
        $ticket = new Ticket();
        $ticket->name = $request->name;
        $ticket->category_id = $request->category_id;
        $ticket->location_id = $location->id;
        $ticket->image_url = $request->image_url;
        $ticket->duration = $request->duration;
        $ticket->status = $request->status;

        // Lưu 2 loại giá
        $ticket->price = $request->price; // Giá thường
        $ticket->price_weekend = $request->price_weekend; // Giá cuối tuần

        $ticket->save();

        return redirect()->route('admin.tickets.index')->with('success', 'Thêm vé thành công!');
    }

    // Form sửa
    public function edit($id)
    {
        $ticket     = Ticket::findOrFail($id);
        $categories = Category::all();
        $locations  = Location::all();

        return view('admin.tickets.edit', compact('ticket', 'categories', 'locations'));
    }

    
    // Cập nhật
    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        // 1. Validate: Giống hệt hàm Store
        $request->validate([
            'name' => 'required',
            'location_name' => 'required|string', 
            'price' => 'required|numeric|min:0',
            'price_weekend' => 'nullable|numeric|min:0',
            'category_id' => 'required',
            'image_url' => 'required',
        ]);

        // 2. Logic Tự động tạo cơ sở 
        $location = Location::firstOrCreate(
            ['name' => $request->location_name],
            [
                'address' => 'Đang cập nhật',
                'hotline' => '0909000000' // Giá trị mặc định tránh lỗi
            ]
        );

        // 3. Cập nhật dữ liệu
        $ticket->name = $request->name;
        $ticket->category_id = $request->category_id;
        $ticket->location_id = $location->id; // Gán ID của cơ sở (vừa tìm hoặc tạo được)
        $ticket->image_url = $request->image_url;
        $ticket->duration = $request->duration;
        $ticket->status = $request->status;
        $ticket->description = $request->description; // Cập nhật mô tả (nếu có)
        
        // Cập nhật 2 loại giá
        $ticket->price = $request->price;
        $ticket->price_weekend = $request->price_weekend;

        $ticket->save();

        return redirect()->route('admin.tickets.index')->with('success', 'Cập nhật vé thành công!');
    }

    // Xóa

    public function destroy($id)
{
    $ticket = Ticket::findOrFail($id);
    $ticket->delete();
    
    return back()->with('success', 'Đã xóa vé thành công!');
}
}