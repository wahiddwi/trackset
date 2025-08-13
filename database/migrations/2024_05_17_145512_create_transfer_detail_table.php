<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('trfdtl_id')->unsigned(); // trf id
            $table->string('rcv_id')->nullable()->unique(); // receive id
            $table->string('trfdtl_transno', 16); // detail asset transno
            $table->string('trfdtl_name'); // trf detail asset name
            $table->string('trfdtl_status')->nullable(); // ONHAND
            $table->string('trfdtl_pic')->nullable(); // user approve
            $table->string('trfdtl_desc', 255)->nullable();
            $table->date('trfdtl_transdate')->nullable(); // tgl transfer
            $table->string('trfdtl_company', 5)->nullable(); // company
            $table->string('trfdtl_site_from');
            $table->integer('trfdtl_loc_from')->unsigned();
            $table->enum('trfdtl_pic_type_from', ['user', 'cabang']);
            $table->string('trfdtl_pic_from'); // asal pic yg pegang barang
            $table->string('trfdtl_site_to');
            $table->integer('trfdtl_loc_to')->unsigned();
            $table->enum('trfdtl_pic_type_to', ['user', 'cabang']);
            $table->string('trfdtl_pic_to'); // destinasi pic yg pegang barang
            $table->integer('trfdtl_order')->nullable();
            $table->string('trfdtl_received_by', 20)->nullable(); // user yg terima barang
            $table->string('created_by', 20);
            $table->string('updated_by', 20);
            $table->string('trfdtl_created_name', 30)->nullable();
            $table->string('trfdtl_updated_name', 30)->nullable();
            $table->string('trfdtl_approver_nik', 10)->nullable();
            $table->string('trfdtl_approver_name', 30)->nullable();

            $table->foreign('trfdtl_id')->references('id')->on('transfer');

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
        Schema::dropIfExists('transfer_detail');
    }
}
