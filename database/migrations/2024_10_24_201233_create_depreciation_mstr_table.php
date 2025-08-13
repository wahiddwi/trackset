<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepreciationMstrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('depreciation_mstr', function (Blueprint $table) {
            $table->id();
            $table->string('dep_company', 10); // company code
            $table->string('dep_doc_ref', 20); // no document
            $table->date('dep_periode'); // date periode Jan-24
            $table->date('dep_eff_date'); // date efective date 31-Jan-24
            $table->string('dep_status'); // OPEN, DONE
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
        Schema::dropIfExists('depreciation_mstr');
    }
}
