<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInsuranceHistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insurance_hist', function (Blueprint $table) {
            $table->id();
            $table->integer('inshist_asset')->unsigned();
            $table->integer('inshist_vendor')->unsigned();
            $table->integer('inshist_vehicle')->unsigned();
            $table->string('inshist_polishno');
            $table->string('inshist_startdate');
            $table->string('inshist_enddate');
            $table->double('inshist_cover', 8, 2);
            $table->double('inshist_premi', 8, 2);

            $table->foreign('inshist_asset')->references('id')->on('inv_mstr');
            $table->foreign('inshist_vendor')->references('id')->on('vendor_mstr');
            $table->foreign('inshist_vehicle')->references('id')->on('vehicle_mstr');
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
        Schema::dropIfExists('insurance_hist');
    }
}
