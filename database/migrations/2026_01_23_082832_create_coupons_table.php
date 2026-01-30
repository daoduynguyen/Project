<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Mã (VD: HOLOMIA20)
            $table->string('type'); // 'percent' (phần trăm) hoặc 'fixed' (tiền mặt)
            $table->decimal('value', 20, 2); // Giá trị giảm
            $table->integer('quantity')->default(100);
            $table->dateTime('expiry_date')->nullable(); // Hạn sử dụng
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
