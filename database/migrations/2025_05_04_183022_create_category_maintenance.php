<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryMaintenance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_maintenance', function (Blueprint $table) {
            $table->id();
            $table->string('mtn_type'); // tipe maintenance
            $table->string('mtn_desc', 255)->nullable();
            $table->boolean('mtn_status')->default(true);
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
        Schema::dropIfExists('category_maintenance');
    }
}
