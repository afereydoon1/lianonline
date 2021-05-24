<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToPruductsAndPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('show_status')->default(false);
            $table->boolean('comment_status')->default(true);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('show_status')->default(false);
            $table->boolean('comment_status')->default(true);
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
            $table->dropColumn('show_status');
            $table->dropColumn('comment_status');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('show_status');
            $table->dropColumn('comment_status');
        });
    }
}
