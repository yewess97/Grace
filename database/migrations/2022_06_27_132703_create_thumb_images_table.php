<?php

use App\Models\Product;
use App\Models\ThumbImage;
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
        Schema::create((new ThumbImage())->getTable(), static function (Blueprint $table) {
            $table->id();
            $table->string(THUMB_IMAGE);
            $table->foreignIdOf(Product::class);
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
        Schema::dropIfExists((new ThumbImage())->getTable());
    }
};
