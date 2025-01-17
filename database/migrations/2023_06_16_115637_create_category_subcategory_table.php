<?php

use App\Models\Category;
use App\Models\CategorySubcategory;
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
    public function up(): void
    {
        Schema::create((new CategorySubcategory())->getTable(), static function (Blueprint $table) {
            $table->foreignIdOf(Category::class);
            $table->foreignIdOf(Subcategory::class);
            $table->index([CATEGORY_ID, SUBCATEGORY_ID]);
            $table->unique([CATEGORY_ID, SUBCATEGORY_ID]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists((new CategorySubcategory())->getTable());
    }
};
