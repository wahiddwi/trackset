<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldLastPeriodeInInvMstrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inv_mstr', function (Blueprint $table) {
            $table->date('inv_last_periode')->nullable(); // last depresiasi Jan-2024 or 1-Jan-2024
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
            $table->dropColumn('inv_last_periode');
        });
    }
}
