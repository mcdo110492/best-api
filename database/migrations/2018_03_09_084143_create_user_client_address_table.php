<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserClientAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('userClientAddress', function (Blueprint $table) {
            $table->increments('addressId');
            $table->unsignedInteger("clientId");
            $table->string('street',50);
            $table->string('city',50);
            $table->string('province',50);
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
        Schema::dropIfExists('userClientAddress');
    }
}
