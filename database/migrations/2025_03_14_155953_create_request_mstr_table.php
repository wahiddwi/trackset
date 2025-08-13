<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestMstrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_mstr', function (Blueprint $table) {
            $table->id();
            $table->string('req_spb', 20);
            $table->date('req_transdate');
            $table->string('req_company')->unsigned();
            $table->string('req_site')->usigned();
            $table->string('req_status'); // PENDING / POST
            $table->integer('req_line');
            $table->string('created_by', 20)->nullable();
            $table->string('updated_by', 20)->nullable();
            $table->string('approver_by', 20)->nullable();
            $table->string('creator_name', 50)->nullable();
            $table->string('approver_name', 50)->nullable();

            $table->foreign('req_company')->references('co_company')->on('companies');
            $table->foreign('req_site')->references('si_site')->on('sites');
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
        Schema::dropIfExists('request_mstr');
    }
}
