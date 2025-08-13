<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJournalLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_logs', function (Blueprint $table) {
            $table->id();
            $table->string('url')->nullable(); // url api
            $table->string('response_code')->nullable(); // response api
            $table->string('response')->nullable(); // response api
            $table->json('logs');
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
        Schema::dropIfExists('journal_logs');
    }
}
