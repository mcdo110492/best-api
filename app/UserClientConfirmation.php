<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserClientConfirmation extends Model
{
    protected $table = "userClientConfirmation";

    protected $primaryKey = "confirmId";

    protected $fillable =[
        "clientId",
        "token",
        "isConfirm",
        "expiredAt"
    ];
}
