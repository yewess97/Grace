<?php

use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Product;
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
        Schema::create((new CategoryProduct())->getTable(), static function (Blueprint $table) {
            $table->foreignIdOf(Category::class);
            $table->foreignIdOf(Product::class);
            $table->index([CATEGORY_ID, PRODUCT_ID]);
            $table->unique([CATEGORY_ID, PRODUCT_ID]);
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
        Schema::dropIfExists((new CategoryProduct())->getTable());
    }
};
