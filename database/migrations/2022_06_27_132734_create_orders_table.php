<?php

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
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
        Schema::create((new Order())->getTable(), static function (Blueprint $table) {
            $table->id();
            $table->string(TRACKING_NUM)->unique()->index();
            $table->unsignedInteger(NUM_ITEMS);
            $table->unsignedDouble(TOTAL_COST);
            $table->unsignedTinyInteger(STATUS)->default(1); // 1 --> processing, 2 --> shipped, 3 --> delivered, 4 --> completed, 5 --> canceled
            $table->unsignedTinyInteger(PAYMENT_METHOD); // 1 --> Stripe, 2 --> Cash On Delivery
            $table->string(PAYMENT_ID)->nullable();
            $table->nullableForeignIdOf(User::class);
            $table->nullableForeignIdOf(Address::class);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    final public function down(): void
    {
        Schema::dropIfExists((new Order())->getTable());
    }
};
