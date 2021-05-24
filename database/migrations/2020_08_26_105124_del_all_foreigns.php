<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DelAllForeigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
        });
        Schema::table('admins', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['wallet_id']);
        });
        Schema::table('referral_links', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('referral_requests', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['referral_link_id']);
        });
        Schema::table('ticket_items', function (Blueprint $table) {
            $table->dropForeign(['ticket_id']);
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['product_id']);
        });
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
