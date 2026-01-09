<?php

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    final public function up(): void
    {
        Schema::create((new OrderItem())->getTable(), static function (Blueprint $table) {
            $table->id();
            $table->string(PRODUCT_NAME, 300);
            $table->string(PRODUCT_MAIN_IMAGE);
            $table->productSizeQuantity();
            $table->unsignedDouble(PRODUCT_TOTAL_PRICE);
            $table->foreignIdOf(Order::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    final public function down(): void
    {
        Schema::dropIfExists((new OrderItem())->getTable());
    }
};
