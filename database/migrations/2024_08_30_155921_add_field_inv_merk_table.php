<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldInvMerkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inv_mstr', function (Blueprint $table) {
            $table->integer('inv_merk')->nullable()->unsigned();
            $table->foreign('inv_merk')->references('id')->on('brand_mstr')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inv_mstr', function (Blueprint $table) {
            $table->dropColumn('inv_merk');
            $table->dropForeign(['inv_merk']);
        });
    }
}
