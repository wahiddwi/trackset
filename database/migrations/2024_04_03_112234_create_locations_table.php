<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loc_mstr', function (Blueprint $table) {
            $table->id();
            $table->string('loc_id', 30)->unique(); // site code - location code
            $table->string('loc_site', 10);
            $table->foreign('loc_site')->references('si_site')->on('sites')->onUpdate('cascade')->onDelete('cascade');
            $table->string('loc_name', 60);
            $table->boolean('loc_active')->default(true);
            $table->string('created_by', 20);
            $table->string('updated_by', 20);
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
        Schema::dropIfExists('loc_mstr');
    }
}
