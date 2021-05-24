<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('username')->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('mobile')->unique()->nullable();
            $table->string('avatar')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('card_number')->nullable();
            $table->string('sheba')->nullable();
            $table->string('national_card_image')->nullable();
            $table->string('credit_card_image')->nullable();
            $table->string('bank')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->text('address')->nullable();
            $table->text('bio')->nullable();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->foreign('role_id')->on('roles')->references('id');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('mobile_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->enum('status', ['active', 'deActive'])->default('active');
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
        Schema::dropIfExists('users');
    }
}
