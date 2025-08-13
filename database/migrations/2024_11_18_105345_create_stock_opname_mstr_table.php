<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockOpnameMstrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_mstr', function (Blueprint $table) {
            $table->id();
            $table->string('stock_transno', 20)->nullable()->unique();
            $table->date('stock_transdate');
            $table->string('stock_desc', 255)->nullable();
            $table->string('stock_status', 10)->default('OPEN'); // OPEN, CLOSE
            $table->string('stock_site',10);
            $table->string('stock_site_name', 50);
            $table->integer('stock_loc')->unsigned();
            $table->string('stock_loc_name', 50);
            $table->integer('stock_itemttl')->default(0);
            $table->integer('stock_found')->default(0); // counting item found
            $table->integer('stock_opname')->default(0); // counting item opname
            $table->integer('stock_additional')->default(0); // counting item additional
            $table->integer('stock_counter')->default(0);

            $table->foreign('stock_site')->references('si_site')->on('sites');
            $table->foreign('stock_loc')->references('id')->on('loc_mstr');
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
        Schema::dropIfExists('stock_mstr');
    }
}
