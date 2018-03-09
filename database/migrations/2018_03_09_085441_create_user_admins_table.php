<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('userAdmins', function (Blueprint $table) {
            $table->increments('adminId');
            $table->string("email",50)->unique();
            $table->string("password",250);
            $table->smallInteger("role");
            $table->smallInteger("status")->default(1);
            $table->string("fullName",150)->nullable();
            $table->string("profilePicture",200)->default("default.jpg");
            $table->string("refreshToken",200)->nullable();
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
        Schema::dropIfExists('userAdmins');
    }
}
