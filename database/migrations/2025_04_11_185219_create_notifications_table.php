<?php

use App\Models\Notification;
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
        Schema::create((new Notification())->getTable(), static function (Blueprint $table) {
            $table->uuid(ID)->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            /*
             $table->json('notifiable_id')->nullable();
             $table->unsignedInteger(collectionId(ADMIN))->storedAs("JSON_UNQUOTE(JSON_EXTRACT(notifiable_id, '$.".collectionId(ADMIN)."'))")->index();
             */
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    final public function down(): void
    {
        Schema::dropIfExists((new Notification())->getTable());
    }
};
