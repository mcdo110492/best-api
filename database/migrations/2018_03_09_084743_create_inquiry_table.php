<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInquiryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inquiry', function (Blueprint $table) {
            $table->increments('inquiryId');
            $table->string('clientNumber',20);
            $table->unsignedInteger("clientId");
            $table->smallInteger("status")->default(0);
            $table->timestamp("dateConfirmed");
            $table->unsignedInteger("adminId");
            $table->string('remarks',150)->nullable();
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
        Schema::dropIfExists('inquiry');
    }
}
