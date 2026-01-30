<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',         // Mã giảm giá
        'type',         // Loại (percent/fixed)
        'value',   
        'quantity',     // Giá trị
        'expiry_date'   // Hạn sử dụng
    ];
}