<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('rules')->nullable();
            $table->text('image_url')->nullable();
            $table->decimal('price', 10, 0);
            $table->decimal('price_weekend', 10, 0);
            $table->integer('duration')->default(10);
            $table->enum('status', ['active', 'maintenance'])->default('active');
            $table->float('avg_rating')->default(5.0);
            $table->integer('play_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
