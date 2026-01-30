<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'note',
        'total_amount',
        'payment_method',
        'status',
        'booking_date'
    ];

    // Quan hệ: 1 Đơn hàng có nhiều Chi tiết vé
    public function bookingDetails()
    {
        return $this->hasMany(BookingDetail::class);
    }

    // Quan hệ: 1 Đơn hàng thuộc về 1 User (hoặc null)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}