<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldCodeItemToTrfDtlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transfer_detail', function (Blueprint $table) {
          $table->string('trfdtl_itemcode')->nullable();
          $table->string('trfdtl_spb')->nullable();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfer_detail', function (Blueprint $table) {
            $table->dropColumn('trfdtl_itemcode');
            $table->dropColumn('trfdtl_spb');
        });
    }
}
