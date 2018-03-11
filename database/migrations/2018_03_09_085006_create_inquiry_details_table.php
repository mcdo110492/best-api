<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInquiryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inquiryDetails', function (Blueprint $table) {
            $table->increments('inquiryDetailsId');
            $table->unsignedInteger("inquiryId");
            $table->string("clientNumber",10);
            $table->string('email',50);
            $table->string("fullName",50);
            $table->string("contactNumber",20);
            $table->string('street',50);
            $table->string('city',50);
            $table->string('province',50);
            $table->string('validIdPath',200);
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
        Schema::dropIfExists('inquiryDetails');
    }
}
