<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryDepreciationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_depreciations', function (Blueprint $table) {
            $table->id();
            $table->string('dep_code', 20)->unique(); // category depreciation code
            $table->integer('dep_periode')->nullable();
            $table->string('dep_type', 10)->nullable(); // month, year, null
            $table->integer('dep_amount_periode')->nullable();
            $table->boolean('dep_active')->default(true);
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
        Schema::dropIfExists('category_depreciations');
    }
}
