<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer', function (Blueprint $table) {
            $table->id();
            $table->string('trf_transno')->unique();
            $table->string('trf_company', 5)->nullable();
            $table->date('trf_transdate')->nullable();
            $table->string('trf_site_from');
            $table->integer('trf_loc_from')->unsigned();
            $table->enum('trf_pic_type_from', ['user', 'cabang']);
            $table->string('trf_pic_from'); // asal pic yg pegang barang
            $table->string('trf_site_to');
            $table->integer('trf_loc_to')->unsigned();
            $table->enum('trf_pic_type_to', ['user', 'cabang']);
            $table->string('trf_pic_to'); // destinasi pic yg pegang barang
            $table->string('trf_desc', 255)->nullable();
            $table->string('trf_status')->default('DRAFT'); // DRAFT, TRF, ONHAND
            $table->integer('trf_count')->nullable();
            $table->string('trf_created_name', 30)->nullable();
            $table->string('trf_updated_name', 30)->nullable();
            $table->string('trf_approver_name', 30)->nullable();
            $table->string('trf_approver_nik', 10)->nullable();

            $table->string('created_by', 20);
            $table->string('updated_by', 20);

            $table->foreign('trf_site_from')->references('si_site')->on('sites');
            $table->foreign('trf_loc_from')->references('id')->on('loc_mstr');
            $table->foreign('trf_site_to')->references('si_site')->on('sites');
            $table->foreign('trf_loc_to')->references('id')->on('loc_mstr');

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
        Schema::dropIfExists('transfer');
    }
}
