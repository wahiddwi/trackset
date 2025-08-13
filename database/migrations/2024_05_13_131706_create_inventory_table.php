<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inv_mstr', function (Blueprint $table) {
            $table->id();
            $table->string('inv_transno')->nullable()->unique(); // autogenerate custom id
            $table->integer('inv_category')->unsigned();
            $table->string('inv_site');
            $table->integer('inv_loc')->unsigned();
            $table->integer('inv_depreciation')->unsigned();
            $table->string('inv_name', 60);
            $table->enum('inv_pic_type', ['user', 'cabang'])->nullable();
            $table->string('inv_pic'); // yang pegang barang
            $table->date('inv_obtaindate'); // tgl perolehan
            $table->double('inv_price', 8, 2);
            $table->string('inv_status')->default('DRAFT'); // DRAFT, POSTING, DISABLED, REJECTED, DISPOSAL
            $table->string('inv_desc', 255)->nullable();
            $table->string('inv_sn'); // serial number or imei
            $table->string('inv_doc_ref'); // document referensi
            $table->double('inv_accumulate_dep', 8, 2)->default(0)->after('inv_doc_ref'); // accumulate depreciation
            $table->double('inv_nominal_dep', 8, 2)->nullable()->after('inv_accumulate_dep'); // nominal depreciation
            $table->date('inv_end_date')->nullable(); // tanggal akhir

            $table->foreign('inv_category')->references('id')->on('categories')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('inv_site')->references('si_site')->on('sites')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('inv_loc')->references('id')->on('loc_mstr')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('inv_depreciation')->references('id')->on('category_depreciations')->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('inv_mstr');
    }
}
