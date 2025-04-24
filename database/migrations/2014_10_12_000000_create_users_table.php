<?php

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
        Schema::create((new User())->getTable(), static function (Blueprint $table) {
            $table->id();
            $table->string(FIRST_NAME, 50);
            $table->string(LAST_NAME, 50);
            $table->string(EMAIL)->unique()->index();
            $table->timestamp(EMAIL.'_verified_at');
            $table->string(PASSWORD);
            $table->boolean(ROLE)->default(0); // 0 --> user (customer), 1 --> admin
            $table->timestamp(LAST_SEEN)->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->index([FIRST_NAME, LAST_NAME]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists((new User())->getTable());
    }
};
