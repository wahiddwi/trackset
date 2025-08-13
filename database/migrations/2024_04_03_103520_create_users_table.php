<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('usr_id');
            // $table->unsignedBigInteger('role_id');
            $table->string('usr_nik', 20)->unique();
            $table->string('usr_name', 150);
            $table->string('password');
            $table->foreignId('role_id')->references('id')->on('roles')->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('usr_status')->default(true);
            $table->rememberToken();
            $table->string('usr_email', 60)->nullable();
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
        Schema::dropIfExists('users');
    }
}
