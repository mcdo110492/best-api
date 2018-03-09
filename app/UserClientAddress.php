<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserClientAddress extends Model
{
    protected $table = "userClientAddress";

    protected $primaryKey = "addressId";

    protected $fillable = [
        "clientId",
        "street",
        "city",
        "province"
    ];
}
