<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellMstrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sell_mstr', function (Blueprint $table) {
          $table->id();
          $table->string('sell_no'); // custom ID selling
          $table->date('sell_transdate'); // tgl. document
          $table->string('sell_company')->unsigned(); // PT
          $table->string('sell_site')->unsigned(); // cabang
          $table->integer('sell_cust_id');
          $table->string('sell_desc', 255);
          $table->string('sell_cust_name');
          $table->string('sell_cust_no');
          $table->string('sell_cust_addr', 255);
          $table->string('sell_cust_telp', 20);
          $table->string("sell_cust_wa", 20)->nullable();
          $table->string("sell_cust_email", 50)->nullable();
          $table->string('sell_status'); // RSV, SELL
          $table->integer('sell_qty_item');
          $table->double('sell_total_price', 8, 2);
          $table->double('sell_amount_dep_price', 8, 2)->nullable(); // jumlah harga depresiasi
          $table->string('sell_created_name')->nullable();
          $table->string('sell_created_nik')->nullable();
          $table->string('sell_approver_name')->nullable();
          $table->string('sell_approver_nik')->nullable();

          $table->foreign('sell_company')->references('co_company')->on('companies');
          $table->foreign('sell_site')->references('si_site')->on('sites');
          $table->foreign('sell_cust_id')->references('id')->on('cust_mstr')->onDelete('CASCADE')->onUpdate('CASCADE');
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
        Schema::dropIfExists('sell_mstr');
    }
}
