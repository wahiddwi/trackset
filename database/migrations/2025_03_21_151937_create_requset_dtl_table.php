<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequsetDtlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_dtl', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reqdtl_id')->references('id')->on('request_mstr');
            $table->string('reqdtl_code')->nullable();
            $table->string('reqdtl_item')->nullable();
            $table->string('reqdtl_uom')->nullable();
            $table->integer('reqdtl_qty')->default(0);
            $table->integer('reqdtl_qty_approve')->default(0);
            $table->integer('reqdtl_qty_send')->default(0);
            $table->integer('reqdtl_qty_purchase')->default(0);
            $table->integer('reqdtl_qty_rcv')->default(0);
            $table->string('reqdtl_trfno')->nullable();
            $table->integer('reqdtl_line');
          
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
        Schema::dropIfExists('request_dtl');
    }
}
