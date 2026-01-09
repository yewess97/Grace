<?php

use App\Models\Product;
use App\Models\ProductSize;
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
        Schema::create((new ProductSize())->getTable(), static function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger(SIZE)->default(3); // 1 --> S, 2 --> M, 3 --> L, 4 --> XL, 5 --> XXL
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
        Schema::dropIfExists((new ProductSize())->getTable());
    }
};
