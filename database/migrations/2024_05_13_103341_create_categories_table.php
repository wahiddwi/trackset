<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('cat_code', 20)->unique();
            $table->string('cat_name');
            $table->boolean('cat_active')->default(true);
            $table->string('cat_asset')->nullable()->unsigned(); // asset / coa // Akun asset tetap
            $table->integer('cat_percent')->nullable(); // percent of depreciation each category
            $table->integer('cat_depreciation')->nullable()->unsigned(); // Akun akumulasi penyusutan
            $table->string('cat_accumulate_depreciation')->nullable()->unsigned(); // Akun akumulasi penyusutan
            $table->string('cat_depreciation_expense')->nullable()->unsigned(); // Akun beban penyusutan
            $table->string('cat_income')->nullable()->unsigned(); // Akun Pendapatan

            $table->foreign('cat_asset')->references('coa_account')->on('coa')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('cat_accumulate_depreciation')->references('coa_account')->on('coa')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('cat_depreciation_expense')->references('coa_account')->on('coa')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('cat_income')->references('coa_account')->on('coa')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('cat_depreciation')->references('id')->on('category_depreciations')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('categories');
    }
}
