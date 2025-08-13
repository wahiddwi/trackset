<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockDtlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_dtl', function (Blueprint $table) {
            $table->id();
            $table->string('stockdtl_transno', 20); // id stock mstr
            $table->string('stockdtl_trn_transno', 20); // id asset
            $table->string('stockdtl_desc', 255)->nullable();
            $table->string('stockdtl_note', 255)->nullable(); // keterangan opname
            $table->string('stockdtl_status', 10)->nullable(); // OPNAME, FOUND
            $table->string('stockdtl_site', 10)->nullable();
            $table->integer('stockdtl_loc')->nullable()->unsigned();
            $table->string('stockdtl_name', 255)->nullable();
            $table->string('stockdtl_pic', 10)->nullable();
            $table->string('stockdtl_pic_name')->nullable();
            $table->date('stockdtl_obtaindate')->nullable();
            $table->string('stockdtl_type', 10)->nullable(); // ADDITIONAL, ITEM
            $table->double('stockdtl_price', 8, 2)->nullable();
            $table->double('stockdtl_current_price', 8, 2)->nullable();
            $table->integer('stockdtl_order')->nullable(); // ordering

            $table->foreign('stockdtl_transno')->references('stock_transno')->on('stock_mstr');
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
        Schema::dropIfExists('stock_dtl');
    }
}
