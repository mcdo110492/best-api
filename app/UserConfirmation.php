<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserConfirmation extends Model
{
    protected $table = "userConfirmation";

    protected $primaryKey = "confirmId";

    protected $fillable =[
        "userId",
        "token",
        "isConfirm",
        "expiredAt"
    ];
}
