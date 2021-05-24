<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddOrderIdToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            // $table->dropForeign(['user_id']);
            // $table->dropForeign(['wallet_id']);
            $table->string('user_type')->after('user_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->on('orders')->references('id')->onDelete('cascade');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->on('products')->references('id')->onDelete('cascade');
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $table->dropForeign(['order_id']);
            $table->dropForeign(['product_id']);
            $table->dropColumn('order_id');
            $table->dropColumn('product_id');
            $table->dropColumn('user_type');
            // $table->foreign('user_id')->on('users')->references('id')->onDelete('cascade');
            // $table->foreign('wallet_id')->on('wallets')->references('id')->onDelete('cascade');
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        });
    }
}
