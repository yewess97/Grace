<?php

use App\Models\Product;
use App\Models\ProductSubcategory;
use App\Models\Subcategory;
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
        Schema::create((new ProductSubcategory())->getTable(), static function (Blueprint $table) {
            $table->foreignIdOf(Subcategory::class);
            $table->foreignIdOf(Product::class);
            $table->index([SUBCATEGORY_ID, PRODUCT_ID]);
            $table->unique([SUBCATEGORY_ID, PRODUCT_ID]);
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
        Schema::dropIfExists((new ProductSubcategory())->getTable());
    }
};
