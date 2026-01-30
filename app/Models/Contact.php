<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    // 1. Khai báo tên bảng 
    protected $table = 'contacts';

    // 2. Khai báo các cột được phép lưu (Mass Assignment)
    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'status' 
    ];
}