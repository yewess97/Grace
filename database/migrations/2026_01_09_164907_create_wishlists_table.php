<?php

use App\Models\Wishlist;
use App\Models\Product;
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
        Schema::create((new Wishlist())->getTable(), static function (Blueprint $table) {
            $table->id();
            $table->foreignIdOf(User::class);
            $table->foreignIdOf(Product::class);
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
        Schema::dropIfExists((new Wishlist())->getTable());
    }
};
