<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sell_detail', function (Blueprint $table) {
          $table->id();
          $table->integer('selldtl_asset_id')->unsigned(); // ID Asset
          $table->integer('selldtl_id')->unsigned(); // ID selling
          $table->string('selldtl_transno'); // asset_transno
          $table->date('selldtl_transdate');
          $table->string('selldtl_asset_name', 50);
          $table->double('selldtl_acc_dep', 8,2); // akumulasi depresiasi
          $table->double('selldtl_dep_price', 8,2); // depresiasi price
          $table->double('selldtl_price', 8, 2); // harga jual
          $table->string('selldtl_status')->nullable();
          $table->string('selldtl_desc', 255)->nullable();
          $table->integer('selldtl_order')->nullable();
          
          $table->foreign('selldtl_id')->references('id')->on('sell_mstr');
          $table->foreign('selldtl_asset_id')->references('id')->on('inv_mstr');
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
        Schema::dropIfExists('sell_detail');
    }
}
