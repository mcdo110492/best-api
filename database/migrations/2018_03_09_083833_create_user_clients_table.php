<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('userClients', function (Blueprint $table) {
            $table->increments('clientId');
            $table->string('email',50)->unique();
            $table->string('password',250);
            $table->string('fullName',150);
            $table->string('profilePicture',200)->default("default.jpg");
            $table->string("contactNumber",20);
            $table->unsignedSmallInteger("status")->default(0);
            $table->string('refreshToken',150)->nullable();
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
        Schema::dropIfExists('userClients');
    }
}
