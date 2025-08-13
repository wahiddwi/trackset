<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldInvhistMerkToHistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inv_hist', function (Blueprint $table) {
          $table->integer('invhist_merk')->nullable()->unsigned();
          $table->foreign('invhist_merk')->references('id')->on('brand_mstr');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inv_hist', function (Blueprint $table) {
          $table->dropColumn('invhist_merk');
          $table->dropForeign(['invhist_merk']);
        });
    }
}
