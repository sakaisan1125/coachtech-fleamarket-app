<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSellerIdToPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('seller_id')->nullable(); // 一時的にNULLを許可
        });

        // 既存データにデフォルトのseller_idを設定
        DB::table('purchases')->update(['seller_id' => 1]); // 適切なデフォルト値に変更

        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('seller_id')->nullable(false)->change(); // NULLを禁止
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['seller_id']);
            $table->dropColumn('seller_id');
        });
    }
}
