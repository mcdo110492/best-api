<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserConfirmationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('userConfirmation', function (Blueprint $table) {
            $table->increments('confirmId');
            $table->unsignedInteger("userId");
            $table->string('token',200);
            $table->smallInteger("isConfirm")->dafault(0);
            $table->timestamp("expiredAt");
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
        Schema::dropIfExists('userConfirmation');
    }
}
