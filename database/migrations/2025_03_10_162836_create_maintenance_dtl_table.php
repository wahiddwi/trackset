<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceDtlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_dtl', function (Blueprint $table) {
            $table->id();
            $table->integer('maindtl_id')->unsigned(); // id maintenance
            $table->date('maindtl_transdate');
            $table->integer('maindtl_asset_id')->unsigned();
            $table->string('maindtl_asset_transno', 20);
            $table->string('maindtl_asset_name', 100);
            $table->string('maindtl_company');
            $table->string('maindtl_site');
            $table->integer('maindtl_vendor')->unsigned(); // id vendor
            $table->double('maindtl_cost', 8, 2)->default(0); // biaya maintenance
            $table->string('maindtl_desc', 255)->nullable();
            $table->string('maindtl_status')->default('DRAFT');
            $table->integer('maindtl_line')->default(0);
            $table->integer('maindtl_count')->default(0);
            
            $table->string('created_by', 20)->nullable();
            $table->string('created_by_name', 50)->nullable();
            $table->string('updated_by', 20)->nullable();
            $table->string('updated_by_name', 50)->nullable();
            $table->string('approved_by', 20)->nullable();
            $table->string('approved_by_name', 50)->nullable();

            $table->foreign('maindtl_id')->references('id')->on('maintenance_mstr');
            $table->foreign('maindtl_asset_id')->references('id')->on('inv_mstr');
            $table->foreign('maindtl_vendor')->references('id')->on('vendor_mstr');
            $table->foreign('maindtl_company')->references('co_company')->on('companies');
            $table->foreign('maindtl_site')->references('si_site')->on('sites');

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
        Schema::dropIfExists('maintenance_dtl');
    }
}
