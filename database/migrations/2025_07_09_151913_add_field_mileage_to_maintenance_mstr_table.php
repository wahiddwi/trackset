<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldMileageToMaintenanceMstrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('maintenance_mstr', function (Blueprint $table) {
        //     $table->integer('main_mileage')->nullable()->default(0);
        // });

        Schema::table('maintenance_dtl', function (Blueprint $table) {
            $table->integer('maindtl_mileage')->nullable()->default(0);
        });

        Schema::table('maintenance_hist', function (Blueprint $table) {
            $table->integer('mainhist_mileage')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('maintenance_mstr', function (Blueprint $table) {
        //     $table->dropColumn('main_mileage');
        // });

        Schema::table('maintenance_dtl', function (Blueprint $table) {
            $table->dropColumn('maindtl_mileage');
        });

        Schema::table('maintenance_hist', function (Blueprint $table) {
            $table->dropColumn('mainhist_mileage');
        });
    }
}
