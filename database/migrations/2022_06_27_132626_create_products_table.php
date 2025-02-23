<?php

use App\Models\Category;
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
    public function up(): void
    {
        Schema::create((new Product())->getTable(), static function (Blueprint $table) {
            $table->id();
            $table->nameSlug(PRODUCT_MODEL);
            $table->text(SHORT_DESCRIPTION);
            $table->longText(LONG_DESCRIPTION);
            $table->string(MAIN_IMAGE);
            $table->unsignedFloat(OLD_PRICE, 6)->nullable();
            $table->unsignedFloat(NEW_PRICE, 6);
            $table->unsignedInteger(QUANTITY)->default(1);
            $table->boolean(STATUS)->default(1); // 0 --> Not Available, 1 --> Available
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists((new Product())->getTable());
    }
};
