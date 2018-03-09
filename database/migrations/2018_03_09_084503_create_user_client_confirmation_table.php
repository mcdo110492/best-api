<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserClientConfirmationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('userClientConfirmation', function (Blueprint $table) {
            $table->increments('confirmId');
            $table->unsignedInteger("clientId");
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
        Schema::dropIfExists('userClientConfirmation');
    }
}
