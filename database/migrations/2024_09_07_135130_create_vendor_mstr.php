<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorMstr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_mstr', function (Blueprint $table) {
            $table->id();
            $table->string('vdr_code', 30)->unique();
            $table->string('vdr_name', 30);
            $table->string('vdr_telp', 15);
            $table->string('vdr_addr', 255);
            $table->string('vdr_desc', 255)->nullable();
            $table->boolean('vdr_status')->default('true');
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
        Schema::dropIfExists('vendor_mstr');
    }
}
