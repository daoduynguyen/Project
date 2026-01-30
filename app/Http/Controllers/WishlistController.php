<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    // Hàm Thả tim / Bỏ tim (Dùng AJAX)
    public function toggle($id)
    {
        $user = Auth::user();
        $ticket = Ticket::findOrFail($id);

        // Hàm toggle của Laravel tự động kiểm tra: 
        // Nếu có rồi thì xóa (bỏ tim), chưa có thì thêm (thả tim)
        $user->favorites()->toggle($ticket->id);

        // Kiểm tra xem hiện tại đang thích hay không để trả về icon tương ứng
        $isFavorited = $user->favorites()->where('ticket_id', $id)->exists();

        return response()->json([
            'status' => 'success',
            'is_favorited' => $isFavorited,
            'message' => $isFavorited ? 'Đã thêm vào yêu thích!' : 'Đã xóa khỏi yêu thích!'
        ]);
    }
}