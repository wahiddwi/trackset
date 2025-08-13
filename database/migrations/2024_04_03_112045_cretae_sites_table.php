<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CretaeSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->string('si_site', 30)->unique();
            $table->string('si_name', 60);
            $table->boolean('si_company_site')->default(false);
            $table->string('si_company', 10)->nullable();
            $table->foreign('si_company')->references('co_company')->on('companies')->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('si_active')->default(true);
            $table->string('created_by', 20)->nullable();
            $table->string('updated_by', 20)->nullable();
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
        Schema::dropIfExists('sites');
    }
}
