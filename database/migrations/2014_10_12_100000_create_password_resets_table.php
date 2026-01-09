<?php

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
        Schema::create(PASSWORD_RESETS_TABLE, static function (Blueprint $table) {
            $table->string(EMAIL)->index();
            $table->string(TOKEN, 500)->unique();
            $table->timestamp(DATES[0])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    final public function down(): void
    {
        Schema::dropIfExists('password_resets');
    }
};
