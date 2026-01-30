<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('tickets', function (Blueprint $table) {
        // 1. Xử lý cột 'price_weekday' 
        if (Schema::hasColumn('tickets', 'price_weekday')) {
            $table->dropColumn('price_weekday');
        }

        // 2. Xử lý cột 'price'
        if (!Schema::hasColumn('tickets', 'price')) {
            $table->decimal('price', 10, 2)->default(0)->after('image_url');
        }

        // 3. Xử lý cột 'price_weekend' 
        if (!Schema::hasColumn('tickets', 'price_weekend')) {
            $table->decimal('price_weekend', 10, 2)->nullable()->after('price');
        }
    });
}

    public function down(): void
    {
        // Hoàn tác 
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'price')) {
                $table->renameColumn('price', 'price_weekday');
            }
        });
    }
};