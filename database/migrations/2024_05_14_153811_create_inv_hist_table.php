<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvHistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inv_hist', function (Blueprint $table) {
            $table->id();
            $table->string('invhist_transno'); // generate item id
            $table->integer('invhist_inv'); // fk inventory
            $table->integer('invhist_category')->unsigned();
            $table->string('invhist_site');
            $table->integer('invhist_loc');
            $table->integer('invhist_depreciation')->unsigned(); // depresiasi
            $table->string('invhist_name', 60); // nama barang
            $table->string('invhist_pic'); // yg pegang barang
            $table->date('invhist_obtaindate'); // tgl perolehan
            $table->double('invhist_price', 8, 2);
            $table->string('invhist_status')->default('ONHAND'); // ONHAND, TRF, DISPOSAL
            $table->string('invhist_desc', 255)->nullable();
            $table->string('invhist_sn'); // serial number or imei
            $table->string('invhist_doc_ref');

            $table->string('created_by', 20);
            $table->string('updated_by', 20);

            $table->foreign('invhist_inv')->references('id')->on('inv_mstr');
            $table->foreign('invhist_category')->references('id')->on('categories');
            $table->foreign('invhist_site')->references('si_site')->on('sites');
            $table->foreign('invhist_loc')->references('id')->on('loc_mstr');
            $table->foreign('invhist_depreciation')->references('id')->on('category_depreciations');

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
        Schema::dropIfExists('inv_hist');
    }
}
