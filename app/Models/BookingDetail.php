<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingDetail extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id', 'ticket_id', 'quantity', 'price'];

    // Quan hệ: Chi tiết này thuộc về Vé nào (để lấy tên vé hiển thị lại)
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}