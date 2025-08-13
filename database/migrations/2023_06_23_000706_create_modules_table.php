<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id('mod_id');
            $table->string('mod_code', 20);
            $table->string('mod_name', 100);
            $table->string('mod_path', 20)->default('#');
            $table->string('mod_desc', 150)->nullable();
            $table->string('mod_icon', 100)->nullable();
            $table->string('mod_parent', 10)->nullable();
            $table->boolean('mod_active')->default(true);
            $table->decimal('mod_order', 8, 0);
            $table->boolean('mod_superuser')->default(false);
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
        Schema::dropIfExists('modules');
    }
}
