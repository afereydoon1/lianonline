<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVisitToPostsAndProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->bigInteger('visit')->default(0);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->bigInteger('visit')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('visit');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('visit');
        });
    }
}
