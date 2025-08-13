<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldCatMaintenanceToMaintenanceDtlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('maintenance_dtl', function (Blueprint $table) {
            $table->integer('maindtl_cat_mtn')->nullable();
            $table->date('maindtl_lastdate')->nullable(); // tgl terakhir maintenance
            $table->foreign('maindtl_cat_mtn')->references('id')->on('category_maintenance');
        });

        Schema::table('maintenance_hist', function (Blueprint $table) {
          $table->integer('mainhist_cat_mtn')->nullable();
          $table->date('mainhist_lastdate')->nullable(); // tgl terakhir maintenance
          // $table->foreign('mainhist_cat_mtn')->references('id')->on('category_maintenance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('maintenance_dtl', function (Blueprint $table) {
          $table->dropForeign(['maindtl_cat_mtn']);
          $table->dropColumn('maindtl_cat_mtn');
          $table->dropColumn('maindtl_lastdate');
        });

        Schema::table('maintenance_hist', function (Blueprint $table) {
          // $table->dropForeign(['mainhist_cat_mtn']);
          $table->dropColumn('mainhist_cat_mtn');
          $table->dropColumn('mainhist_lastdate');
        });
    }
}
