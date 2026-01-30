<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact; // Gọi model Contact

//Liên hệ 
class ContactController extends Controller
{
    // Hàm hiển thị form liên hệ
    public function index()
    {
        return view('contact'); // Đường dẫn đến file view contact.blade.php
    }

    // Xử lý lưu tin nhắn vào Database
    public function send(Request $request)
    {
        // 1. Validate (Kiểm tra dữ liệu)
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            // Validate SĐT: Bắt buộc, phải là số, độ dài từ 10-11 số
            'phone' => 'required|numeric|digits_between:10,11',
            'message' => 'required|string',
        ], [
            'email.required' => 'Vui lòng nhập Email.',
            'email.email' => 'Email không đúng định dạng.',
            'phone.required' => 'Vui lòng nhập Số điện thoại.',
            'phone.numeric' => 'Số điện thoại chỉ được chứa số.',
            'phone.digits_between' => 'Số điện thoại phải có 10 hoặc 11 số.',
        ]);

        // 2. Lưu vào Database
        Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $request->message,
            'status' => 'new',
        ]);

        return redirect()->back()->with('success', 'Gửi thông tin thành công! Chúng tôi sẽ liên hệ lại qua SĐT sớm nhất.');
    }
}