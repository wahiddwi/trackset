<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cust_mstr', function (Blueprint $table) {
            $table->id();
            $table->string('cust_no', 30); // cust no or NIK
            $table->string('cust_type', 10); // KTP, SIM, PASSPORT
            $table->string('cust_name', 30);
            $table->string('cust_addr', 255);
            $table->string('cust_telp', 20);
            $table->string('cust_wa', 20)->nullable();
            $table->string('cust_email', 50)->nullable();
            $table->boolean('cust_active')->default(true); // true or false
            $table->string('cust_internal', 8); // true or false
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
        Schema::dropIfExists('cust_mstr');
    }
}
