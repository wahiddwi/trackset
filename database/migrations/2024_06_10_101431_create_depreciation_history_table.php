<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepreciationHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('depreciation_hist', function (Blueprint $table) {
            $table->id();
            $table->string('dephist_transno', 20)->nullable()->unique();
            $table->string('dephist_asset_transno', 20)->unsigned()->nullable(); // inv_transno
            $table->string('dephist_company', 10)->nullable();
            $table->string('dephist_site', 10)->nullable();
            $table->date('dephist_transdate');
            $table->string('dephist_acc_asset', 20)->nullable(); // akun asset (coa asset)
            $table->string('dephist_acc_accumulate_dep', 20)->nullable(); // akun Akumulasi Penyusutan (coa Akumulasi Penyusutan)
            $table->string('dephist_acc_depreciation_expense', 20)->nullable(); // akun Beban Penyusutan (coa Beban Penyusutan)
            $table->string('dephist_acc_income', 20)->nullable(); // akun Pendapatan (coa Pendapatan)
            $table->string('dephist_acc_disposal', 20)->nullable(); // akun Disposal (coa disposal)
            $table->double('dephist_price', 8, 2)->default(0); // harga awal aseet
            $table->double('dephist_accumulate_dep', 8, 2)->default(0); // akumulasi depresiasi
            $table->double('dephist_nominal_dep', 8, 2)->default(0); // nominal depresiasi
            $table->double('dephist_current_price', 8, 2)->default(0); // harga setelah di depresiasi
            
            $table->foreign('dephist_asset_transno')->references('inv_transno')->on('inv_mstr');
            $table->foreign('dephist_company')->references('co_company')->on('companies');
            $table->foreign('dephist_site')->references('si_site')->on('sites');
            $table->foreign('dephist_acc_asset')->references('coa_account')->on('coa');
            $table->foreign('dephist_acc_accumulate_dep')->references('coa_account')->on('coa');
            $table->foreign('dephist_acc_depreciation_expense')->references('coa_account')->on('coa');
            $table->foreign('dephist_acc_income')->references('coa_account')->on('coa');
            $table->foreign('dephist_acc_disposal')->references('coa_account')->on('coa');
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
        Schema::dropIfExists('depreciation_hist');
    }
}
