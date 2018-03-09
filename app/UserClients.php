<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserClients extends Model
{
    protected $table = "userClients";

    protected $primaryKey = "clientId";

    protected $fillable = [
        "email",
        "password",
        "fullName",
        "profilePicture",
        "refreshToken",
        "contactNumber"
    ];
}
