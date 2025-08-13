<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisposalMstrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disposal_mstr', function (Blueprint $table) {
          $table->id();
          $table->string('dis_transno', 20);
          $table->date('dis_transdate');
          $table->string('dis_company')->unsigned();
          // $table->string('dis_site')->unsigned();
          $table->string('dis_status'); // DISPOSAL,ONHAND
          $table->string('dis_desc', 255);
          // $table->string('created_by')->nullable();
          // $table->string('created_by_name')->nullable();
          // $table->string('approver_by')->nullable();
          // $table->string('approver_by_name')->nullable();
          $table->string('created_by', 20)->nullable();
          $table->string('updated_by', 20)->nullable();
          $table->string('approved_by', 20)->nullable();
          
          $table->string('created_name')->nullable();
          $table->string('updated_name')->nullable();
          $table->string('approved_name')->nullable();

          $table->foreign('dis_company')->references('co_company')->on('companies');
          // $table->foreign('dis_site')->references('si_site')->on('sites');
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
        Schema::dropIfExists('disposal_mstr');
    }
}
