<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserValidId extends Model
{
    protected $table = "userValidID";

    protected $primaryKey = "validId";

    protected $fillable = [
        "userId",
        "validIdPath"
    ];
}
