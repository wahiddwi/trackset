<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldInvTagToInvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inv_mstr', function (Blueprint $table) {
          $table->integer('inv_tag')->nullable();
          $table->foreign('inv_tag')->references('id')->on('tag_mstr')->onUpdate('CASCADE')->onDelete('CASCADE');
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
            $table->dropColumn('inv_tag');
            $table->dropForeign(['inv_tag']);
        });
    }
}
