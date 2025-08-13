<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_user', function (Blueprint $table) {
            $table->foreignId('su_user')->references('usr_id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('su_site', 10);
            $table->foreign('su_site')->references('si_site')->on('sites')->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('su_default')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_user');
    }
}
