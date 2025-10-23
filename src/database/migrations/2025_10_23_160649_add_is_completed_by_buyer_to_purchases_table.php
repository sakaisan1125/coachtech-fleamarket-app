<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsCompletedByBuyerToPurchasesTable extends Migration
{
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->boolean('is_completed_by_buyer')->default(false)->after('completed_at'); // 購入者が取引を完了したか
        });
    }

    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('is_completed_by_buyer');
        });
    }
}