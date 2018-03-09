<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAdmins extends Model
{
    protected $table = "userAdmins";

    protected $primaryKey = "adminId";

    protected $fillable = [
        "enail",
        "password",
        "role",
        "status",
        "refreshToken",
        "fullName",
        "profilePicture"
    ];
}
