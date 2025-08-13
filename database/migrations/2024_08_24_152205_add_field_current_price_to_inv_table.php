<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldCurrentPriceToInvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inv_mstr', function (Blueprint $table) {
            $table->double('inv_current_price', 8, 2)->nullable();
            $table->integer('inv_dep_periode')->nullable(); // total depresiasi
            $table->integer('inv_dep_amount')->nullable(); // jumlah berapa kali terdepresiasi
            $table->string('inv_company', 20)->nullable(); // PT
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inv_mstr', function (Blueprint $table) {
            $table->dropColumn('inv_current_price');
            $table->dropColumn('inv_dep_periode');
            $table->dropColumn('inv_dep_amount');
            $table->dropColumn('inv_company');
        });
    }
}
