<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralParamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_param', function (Blueprint $table) {
            $table->id();
            $table->string('param_sales_profit')->nullable()->unsigned(); // laba penjualan asset tetap
            $table->string('param_sales_loss')->nullable()->unsigned(); // rugi penjualan asset tetap
            $table->string('param_expense_loss')->nullable()->unsigned(); // rugi beban write off
            $table->string('param_asset_transaction')->nullable()->unsigned(); // transaksi aktiva tetap
            $table->string('param_cash')->nullable()->unsigned(); // Kas
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
        Schema::dropIfExists('general_param');
    }
}
