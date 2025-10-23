<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingsTable extends Migration
{
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id'); // 取引ID
            $table->unsignedBigInteger('rated_user_id'); // 評価されたユーザーID
            $table->unsignedBigInteger('rater_user_id'); // 評価したユーザーID
            $table->integer('rating'); // 評価（1〜5）
            $table->timestamps();

            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
            $table->foreign('rated_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('rater_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ratings');
    }
}