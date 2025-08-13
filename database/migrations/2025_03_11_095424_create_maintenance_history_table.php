<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_hist', function (Blueprint $table) {
            $table->id();
            $table->integer('mainhist_main_id')->unsigned(); // id maintenance
            $table->date('mainhist_transdate');
            $table->integer('mainhist_asset_id')->unsigned();
            $table->string('mainhist_asset_transno', 20);
            $table->string('mainhist_asset_name', 100);
            $table->string('mainhist_company');
            $table->string('mainhist_site');
            $table->integer('mainhist_vendor')->unsigned(); // id vendor
            $table->double('mainhist_cost', 8, 2)->default(0); // biaya maintenance
            $table->string('mainhist_desc', 255)->nullable();
            $table->integer('mainhist_count')->default(0);

            $table->string('created_by', 20)->nullable();
            $table->string('created_by_name', 50)->nullable();
            $table->string('updated_by', 20)->nullable();
            $table->string('updated_by_name', 50)->nullable();
            $table->string('approver_by', 20)->nullable();
            $table->string('approver_by_name', 50)->nullable();
            
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
        Schema::dropIfExists('maintenance_hist');
    }
}
