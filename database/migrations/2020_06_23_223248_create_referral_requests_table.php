<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->on('orders')->references('id')->onDelete('cascade');
            $table->unsignedBigInteger('referral_link_id')->nullable();
            $table->foreign('referral_link_id')->on('referral_links')->references('id')->onDelete('cascade');
            $table->string('referral_code')->nullable();
            $table->string('landing')->nullable();
            $table->string('host')->nullable();
            $table->string('ip')->nullable();
            $table->boolean('valid')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referral_requests');
    }
}
