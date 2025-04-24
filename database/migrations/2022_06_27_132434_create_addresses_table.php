<?php

use App\Models\Address;
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
        Schema::create((new Address())->getTable(), static function (Blueprint $table) {
            $table->id();
            $table->mediumText(ADDRESS1);
            $table->mediumText(ADDRESS2)->nullable();
            $table->string(CITY, 50);
            $table->string(STATE, 50)->nullable();
            $table->string(COUNTRY)->index();
            $table->unsignedInteger(POSTAL_CODE);
            $table->foreignIdOf(User::class);
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
        Schema::dropIfExists((new Address())->getTable());
    }
};
