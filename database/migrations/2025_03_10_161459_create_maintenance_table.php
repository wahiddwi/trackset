<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_mstr', function (Blueprint $table) {
            $table->id();
            $table->string('main_transno', 20)->unique();
            $table->integer('main_vendor')->unsigned();
            $table->string('main_company', 10);
            $table->date('main_transdate');
            $table->string('main_status')->default('DRAFT');
            $table->integer('asset_count')->default(0);
            $table->double('main_total_cost', 8, 2)->default(0);

            $table->string('created_by', 20)->nullable();
            $table->string('created_by_name', 50)->nullable();
            $table->string('updated_by', 20)->nullable();
            $table->string('updated_by_name', 50)->nullable();
            $table->string('approver_by', 20)->nullable();
            $table->string('approver_by_name', 50)->nullable();

            $table->foreign('main_vendor')->references('id')->on('vendor_mstr');
            $table->foreign('main_company')->references('co_company')->on('companies');
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
        Schema::dropIfExists('maintenance_mstr');
    }
}
