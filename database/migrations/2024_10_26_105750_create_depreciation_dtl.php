<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepreciationDtl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('depreciation_dtl', function (Blueprint $table) {
            $table->id();
            $table->integer('depdtl_doc_id')->unsigned(); // depreciation master id
            $table->string('depdtl_asset_transno', 20);
            $table->string('depdtl_company', 10);
            $table->string('depdtl_site', 10);
            $table->integer('depdtl_category');
            $table->string('depdtl_acc_accumulate_dep', 10);
            $table->string('depdtl_acc_expense_dep', 10);
            // $table->string('depdtl_acc_fixed_asset', 10);
            $table->double('depdtl_asset_price', 8, 2);
            $table->double('depdtl_nominal_dep', 8, 2);
            $table->double('depdtl_accumulate_dep', 8, 2);
            $table->double('depdtl_current_price', 8, 2);
            $table->integer('depdtl_dep_amount');
            $table->string('depdtl_desc', 255)->nullable();
            $table->string('depdtl_doc_ref');

            $table->foreign('depdtl_doc_id')->references('id')->on('depreciation_mstr');
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
        Schema::dropIfExists('depreciation_dtl');
    }
}
