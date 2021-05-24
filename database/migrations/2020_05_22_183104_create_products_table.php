<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('creator_id');
            $table->string('creator_type');
            $table->unsignedInteger('price')->default(0);
            $table->unsignedInteger('total')->default(1);
            $table->unsignedInteger('weight')->default(0);
            $table->unsignedInteger('discounted_price')->nullable();
            $table->unsignedInteger('marketer_percent')->default(0);
            $table->string('key')->unique()->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['physical', 'file'])->default('physical');
            $table->unsignedBigInteger('order')->default(0);
            $table->enum('status', ['active', 'deActive'])->default('active');
            $table->text('history')->nullable();
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
        Schema::dropIfExists('products');
    }
}
