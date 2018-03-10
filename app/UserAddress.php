<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $table = "userAddress";

    protected $primaryKey = "addressId";

    protected $fillable = [
        "userId",
        "street",
        "city",
        "province"
    ];
}
