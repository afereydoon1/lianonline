<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinWithdrawalAmountToGatewayConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gateway_configs', function (Blueprint $table) {
            $table->unsignedBigInteger('min_withdrawal_amount')->default(1000);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gateway_configs', function (Blueprint $table) {
            $table->dropColumn('min_withdrawal_amount');
        });
    }
}
