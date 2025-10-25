<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifySellerIdDefaultInPurchasesTable extends Migration
{
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('seller_id')->default(1)->change();
        });
    }

    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('seller_id')->default(null)->change();
        });
    }
}