<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisposalDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disposal_detail', function (Blueprint $table) {
          $table->id();
          $table->integer('disdtl_dis_id');
          $table->date('disdtl_transdate');
          $table->string('disdtl_asset_transno');
          $table->string('disdtl_asset_name');
          $table->string('disdtl_asset_site');
          $table->integer('disdtl_order'); // urutan
          $table->string('disdtl_status'); // ONHAND, RSV, DISPOSAL
          // $table->string('created_by')->nullable();
          // $table->string('created_by_name')->nullable();
          // $table->string('approver_by')->nullable();
          // $table->string('approver_by_name')->nullable();
          $table->string('disdtl_desc', 255)->nullable();

          $table->string('created_by', 20)->nullable();
          $table->string('updated_by', 20)->nullable();
          $table->string('approved_by', 20)->nullable();
          
          $table->string('created_name')->nullable();
          $table->string('updated_name')->nullable();
          $table->string('approved_name')->nullable();


          $table->foreign('disdtl_dis_id')->references('id')->on('disposal_mstr');
          $table->foreign('disdtl_asset_site')->references('si_site')->on('sites');
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
        Schema::dropIfExists('disposal_detail');
    }
}
