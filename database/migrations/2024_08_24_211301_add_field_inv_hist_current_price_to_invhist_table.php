<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldInvHistCurrentPriceToInvhistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inv_hist', function (Blueprint $table) {
            $table->double('invhist_cur_price', 8, 2)->nullable(); // harga setelah di depresiasi
            $table->integer('invhist_dep_periode')->nullable(); // total depresiasi
            $table->integer('invhist_dep_amount')->nullable(); // jumlah berapa kali terdepresiasi
            $table->string('invhist_company', 20)->nullable(); // PT
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
            $table->dropColumn('invhist_cur_price');
            $table->dropColumn('invhist_dep_periode');
            $table->dropColumn('invhist_dep_amount');
            $table->dropColumn('invhist_company');
        });
    }
}
