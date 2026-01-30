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
    Schema::create('wishlists', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade'); // Giả sử bảng vé tên là 'tickets'
        $table->timestamps();
        
        // Đảm bảo 1 người không thích 1 vé 2 lần
        $table->unique(['user_id', 'ticket_id']); 
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
