<?php

use App\Models\Product;
use App\Models\Review;
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
    public function up(): void
    {
        Schema::create((new Review())->getTable(), static function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger(RATING);
            $table->string(TITLE, 70);
            $table->text(BODY_TEXT);
            $table->foreignIdOf(Product::class);
            $table->foreignIdOf(User::class);
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
        Schema::dropIfExists((new Review())->getTable());
    }
};
