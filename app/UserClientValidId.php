<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserClientValidId extends Model
{
    protected $table = "userClientValidID";

    protected $primaryKey = "validId";

    protected $fillable = [
        "clientId",
        "validIdPath"
    ];
}
