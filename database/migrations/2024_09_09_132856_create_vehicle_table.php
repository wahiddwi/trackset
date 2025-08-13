<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_mstr', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_transno', 30)->unique();
            $table->string('vehicle_no', 10); // no plat kendaraan
            $table->string('vehicle_name', 60); // nama kendaraan
            $table->integer('vehicle_brand')->unsigned();
            $table->string('vehicle_identityno', 40); // no. rangka kendaraan
            $table->string('vehicle_engineno', 40); // no. mesin
            $table->string('vehicle_color');
            $table->string('vehicle_documentno'); // no. stnk
            $table->integer('vehicle_capacity'); // cc
            $table->integer('vehicle_last_km')->nullable(); // last km
            $table->string('vehicle_desc', 255)->nullable();
            $table->string('vehicle_status', 20)->nullable(); // cover asuransi
            $table->string('created_by', 20)->nullable();
            $table->string('created_by_name', 50)->nullable();
            $table->string('updated_by', 20)->nullable();
            $table->string('updated_by_name', 50)->nullable();

            $table->foreign('vehicle_transno')->references('inv_transno')->on('inv_mstr');
            $table->foreign('vehicle_brand')->references('id')->on('brand_mstr');
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
        Schema::dropIfExists('vehicle_mstr');
    }
}
